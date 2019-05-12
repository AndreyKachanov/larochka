<?php

namespace App\Console\Commands\Email;

use App\Services\Parser\ParseMailBox;
use Illuminate\Console\Command;
use App\Entity\Email\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ParseMailBoxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse MS Exchange mail box';

    /**
     * @var ParseMailBox
     */
    private $service;

    /**
     * @var array
     */
    private $foldersFromExchange;

    public function __construct(ParseMailBox $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {
        $maxEntriesReturned = (int)config('mail.ms_exchange_count_parsing');
        // список папок из конфига
        $this->foldersFromExchange = $this->service->getFoldersFromExchange(config('mail.ms_exchange_parse_folders'));

        //максимум 150 писем распарсивать за раз
        if ($maxEntriesReturned > 150 || $maxEntriesReturned == 0) {
            $this->error("Value MS_EXCHANGE_COUNT_PARSING must be from 1 to 150");
            return false;
        }
        // если таблица пустая - делаем первую выборку из почты, первой папки что указана в конфиге
        if (Message::count() == 0) {
            $this->handleMessages(0, $maxEntriesReturned, $this->foldersFromExchange[0]);
            // иначе подсчитываем сколько в бд хранится по каждой папке записей.
        } else {
            // извлекаем сгруппированный массив по папкам с количество в каждой
            $foldersFromDb = Message::all()->groupBy('folder_id')->map(function ($t) {
                /**@var Collection $t */
                return $t->count();
            })->toArray();

            foreach ($this->foldersFromExchange as $folderFromExchange) {
                //если в бд есть папка, которая указана в конфиге
                if (array_key_exists($folderFromExchange['folder_id'], $foldersFromDb)) {
                    //получаем кол-во писем в бд по этой папке
                    $offset = $foldersFromDb[$folderFromExchange['folder_id']];
                    //записываем в бд данные из нужной папки и нужным шагом
                    if ($result = $this->handleMessages($offset, $maxEntriesReturned, $folderFromExchange)) {
                        return true;
                    } else {
                        continue;
                    }
                    //если в бд нет папки, которая указана в конфиге - записываем в бд данные по новой папке
                } else {
                    if ($result = $this->handleMessages(0, $maxEntriesReturned, $folderFromExchange)) {
                        return true;
                    //если данные в новой папке нет данных - следующая итерация цикла
                    } else {
                        continue;
                    }
                }
            }
            //dd("finish");
        }
        return false;
    }

    private function handleMessages(int $offset, int $maxEntriesReturned, array $folder): bool
    {
        // получаем айдишники сообщений из почты. Если выборка пустая, вернется пустой массив
        $messagesIds = $this->service->getMessagesIds($offset, $maxEntriesReturned, $folder);

        if (empty($messagesIds)) {
            $errorMsg = sprintf(
                'Parsing of folder >>%s<<. Missing data, offset %d, max entries %d.',
                $folder['name'],
                $offset,
                $maxEntriesReturned
            );
            $this->error($errorMsg);
            return false;
        }

        $dataFromDB = $this->service->getDataFromDb($messagesIds, $folder);
        $result = $this->service->insertToDatabase($dataFromDB);
        $this->clearCache();

        if ($result) {
            $resultMsg = sprintf(
                'Parsing of folder >>%s<<. Parsing successfully completed. Offset %d, max entries %d, inserted rows - %d',
                $folder['name'],
                $offset,
                $maxEntriesReturned,
                count($dataFromDB)
            );

            $this->info($resultMsg);
        }

        return true;
    }

    private function clearCache()
    {
        Cache::tags([
            'emails',
            'senders',
            'ipAddress',
            'regions',
            'cities',
            'providers',
            'countries'
        ])->flush();
    }
}
