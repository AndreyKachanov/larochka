<?php

namespace App\Console\Commands\Email;

use App\Services\Parser\ParseMailBox;
use Illuminate\Console\Command;
use App\Entity\Email\Message;
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

        Cache::tags([
            'messages',
            'emails',
            'senders',
            'ipAddress',
            'regions',
            'cities',
            'providers',
            'countries'
        ])->flush();

        $this->service = $service;
        $this->foldersFromExchange = $this->service->getFoldersFromExchange(config('mail.ms_exchange_parse_folders'));
        //dd($this->foldersFromExchange);
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {

        //0) если на почте существует первая папка из конфига, делаем из неё выборку, записываем в бд с меткой папки
        //1) чекать последнюю запись
        //2) определить к какой папке принадлежит
        //3) проверить, есть ли такая папка  в файле конфига
        //4) если есть - посчитать в бд кол-во записей
        //с меткой этой папки, далее делаем выборку из почты от этого кол-ва в бд до шага, записываем в бд
        //5) если папки в конфиге нет,

        $maxEntriesReturned = (int)config('mail.ms_exchange_count_parsing');

        if ($maxEntriesReturned >= 150 || $maxEntriesReturned == 0) {
            $this->error("Value MS_EXCHANGE_COUNT_PARSING must be from 1 to 150");
            return false;
        }
        // если таблица пустая - делаем первую выборку из почты, первой папки что указана в конфиге
        if (Message::count() == 0) {
            $offset = 0;
            $this->handleMessages($offset, $maxEntriesReturned, $this->foldersFromExchange[0]);
            // иначе получаем кол-во строк в бд, после делаем выборку эт этого кол-ва с шагом в 1000
        } else {
            $lastFolderId = Message::orderByDesc('id')->first()->folder_id;

            //если айдишник папки последней записи есть в .env
            $key = array_search($lastFolderId, array_column($this->foldersFromExchange, 'folder_id'));
            if ($key !== false) {
                $offset = Message::whereFolderId($lastFolderId)->count();

                $result = $this->handleMessages($offset, $maxEntriesReturned, $this->foldersFromExchange[$key]);

                //если по первой папке из .env данных в почте уже нет, идем в другую папку
                if (!$result) {
                    //если в .env есть другая папка
                    if (isset($this->foldersFromExchange[$key + 1])) {
                        // берем сделующую папку из .env, ищем по ней кол-во записей в бд, извлекаем
                        // из почты, записываем в бд
                        $nextFolder = $this->foldersFromExchange[$key + 1];


                        //dd($nextFolder);
                        $offset = Message::whereFolderId($nextFolder['folder_id'])->count();

                        $result = $this->handleMessages($offset, $maxEntriesReturned, $nextFolder);
                        if ($result) {
                            return true;
                        }
                        //dd($this->foldersFromExchange[0]);
                    } else {
                        $this->info("finish");
                    }
                }
            } else {
                $this->info('Не найдена папка, которая соответствует последней записи в бд!!!');
            }
        }

        return true;
    }

    private function handleMessages(int $offset, int $maxEntriesReturned, array $folder): bool
    {

        // получаем айдишники сообщений из почты. Если выборка пустая, вернется пустой массив
        $messagesIds = $this->service->getMessagesIds($offset, $maxEntriesReturned, $folder);

        if (empty($messagesIds)) {
            $this->error('Parsing of folder >> ' . $folder['name'] . ' <<. Missing data. offset ' . $offset . ', max entries ' . $maxEntriesReturned . '.');
            return false;
        }

        $dataFromDB = $this->service->getDataFromDb($messagesIds, $folder);
        //dd($dataFromDB);
        $result = $this->service->insertToDatabase($dataFromDB);
        //Cache::tags([
        //    'messages',
        //    'emails',
        //    'senders',
        //    'ipAddress',
        //    'regions',
        //    'cities',
        //    'providers',
        //    'countries'
        //])->flush();

        if ($result) {
            $this->info('Parsing of folder >> ' . $folder['name'] . ' <<. Parsing successfully completed. offset ' . $offset . ', max entries ' . $maxEntriesReturned . ', inserted rows - ' . count($dataFromDB) . '.');
        }

        return true;
    }
}
