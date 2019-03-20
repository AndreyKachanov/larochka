<?php

namespace App\Services\Parser;

use App\Entity\Email\Message;
use Carbon\Carbon;
use Illuminate\Support\Str;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfFieldOrdersType;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Enumeration\ContainmentComparisonType;
use jamesiarmes\PhpEws\Enumeration\ContainmentModeType;
use jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType;
use jamesiarmes\PhpEws\Request\FindFolderType;
use jamesiarmes\PhpEws\Request\GetFolderType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Type\ContainsExpressionType;
use jamesiarmes\PhpEws\Type\FieldOrderType;
use jamesiarmes\PhpEws\Type\FolderIdType;
use jamesiarmes\PhpEws\Type\FolderResponseShapeType;
use jamesiarmes\PhpEws\Type\IndexedPageViewType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfPathsToElementType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\Type\IsGreaterThanOrEqualToType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\IsLessThanOrEqualToType;
use jamesiarmes\PhpEws\Type\RestrictionType;
use jamesiarmes\PhpEws\Type\AndType;
use jamesiarmes\PhpEws\Enumeration\ItemQueryTraversalType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeResponseType;
use DateTime;
use Exception;
use GuzzleHttp\Client as GuzzleClient;

class ParseMailBox
{

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->setCurlOptions([CURLOPT_SSL_VERIFYPEER => false]);
    }

    public function getMessagesIds(int $offset, int $maxEntriesReturned, array $folder): array
    {
//        $request_folder_inbox = new GetFolderType();
//        $request_folder_inbox->FolderShape = new FolderResponseShapeType();
//        $request_folder_inbox->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
//        $request_folder_inbox->FolderIds = new NonEmptyArrayOfBaseFolderIdsType();
//        $request_folder_inbox->FolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
//        $request_folder_inbox->FolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;
//        $response_folder_inbox = $this->client->GetFolder($request_folder_inbox);

        // Кол-во писем в INBOX
        // dump($response_folder_inbox->ResponseMessages->GetFolderResponseMessage[0]->Folders->Folder[0]->TotalCount);

        $request = new FindItemType;
        $request->ItemShape = new ItemResponseShapeType;
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;
        $request->Traversal = ItemQueryTraversalType::SHALLOW;

        // Извлекаем объекты из папки INBOX
//        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType;
//        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType;
//        $request->ParentFolderIds->DistinguishedFolderId->Id = [];
//        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;


//        Извлекаем объекты не из папки INBOX (по айдишнику)
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->ParentFolderIds->FolderId = new FolderIdType();
        $request->ParentFolderIds->FolderId->Id = $folder['folder_id'];


        // sort order
        $request->SortOrder = new NonEmptyArrayOfFieldOrdersType;
        $request->SortOrder->FieldOrder = [];
        $order = new FieldOrderType;

        // sorts mails so that oldest appear first
        // more field uri definitions can be found from types.xsd (look for UnindexedFieldURIType)
        $order->FieldURI = '';
        // @ symbol stops the creating default object from empty value error
        @$order->FieldURI->FieldURI = 'item:DateTimeReceived';
        $order->Order = 'Ascending';
        $request->SortOrder->FieldOrder[] = $order;

        // Build the request for the parts.
        $parts_request = new GetItemType;
        $parts_request->ItemShape = new ItemResponseShapeType;
        $parts_request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;

        // You can get the body as HTML, text or "best".
        $parts_request->ItemShape->BodyType = BodyTypeResponseType::TEXT;

        // Add the body property.
        $body_property = new PathToUnindexedFieldType;
        $body_property->FieldURI = 'item:InternetMessageHeaders';
        $body_property2 = new PathToUnindexedFieldType;
        $body_property2->FieldURI = 'message:Sender';
        $body_property3 = new PathToUnindexedFieldType;
        $body_property3->FieldURI = 'item:DateTimeSent';

        $parts_request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType;
        $parts_request->ItemShape->AdditionalProperties->FieldURI = [$body_property, $body_property2, $body_property3];


        $parts_request->ItemIds = new NonEmptyArrayOfBaseItemIdsType;
        $parts_request->ItemIds->ItemId = [];

        // // Limits the number of items retrieved
        $request->IndexedPageItemView = new IndexedPageViewType;
        $request->IndexedPageItemView->BasePoint = "Beginning";

        $request->IndexedPageItemView->Offset = $offset;
        $request->IndexedPageItemView->MaxEntriesReturned = $maxEntriesReturned;

        try {
            $response = $this->client->FindItem($request);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        if (isset($response)) {
            try {
                $messageIds = $response->ResponseMessages->FindItemResponseMessage[0]->RootFolder->Items->Message;

                if (empty($messageIds)) {
                    return [];
                }

                foreach ($messageIds as $item) {
                    $ids[] = $item->ItemId->Id;
                }
            } catch (Exception $e) {
                dd($e->getMessage());
            }
        }

//        dd($ids);
        return $ids;
    }

    public function getDataFromDb(array $messagesIds, array $folder): array
    {
        // Build the request for the parts.
        $parts_request = new GetItemType;
        $parts_request->ItemShape = new ItemResponseShapeType;
        $parts_request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;

        // You can get the body as HTML, text or "best".
        $parts_request->ItemShape->BodyType = BodyTypeResponseType::TEXT;

        // Add the body property.
        $body_property = new PathToUnindexedFieldType;
        $body_property->FieldURI = 'item:InternetMessageHeaders';
        $body_property2 = new PathToUnindexedFieldType;
        $body_property2->FieldURI = 'message:Sender';
        $body_property3 = new PathToUnindexedFieldType;
        // $body_property3->FieldURI = 'item:DateTimeSent';
        $body_property3->FieldURI = 'item:DateTimeReceived';
        $body_property4 = new PathToUnindexedFieldType;
        $body_property4->FieldURI = 'item:ParentFolderId';

        $parts_request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType;
        $parts_request->ItemShape->AdditionalProperties->FieldURI = [
            $body_property,
            $body_property2,
            $body_property3,
            $body_property4
        ];

        $parts_request->ItemIds = new NonEmptyArrayOfBaseItemIdsType;
        $parts_request->ItemIds->ItemId = [];


        foreach ($messagesIds as $messagesId) {
            $message_item = new ItemIdType;
            $message_item->Id = $messagesId;
            $parts_request->ItemIds->ItemId[] = $message_item;
        }

        try {
            $messages_response = $this->client->GetItem($parts_request)->ResponseMessages->GetItemResponseMessage;
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        foreach ($messages_response as $message) {
//            dump($message);

            if ($message->MessageText === null) {
                $dateReceived = Carbon::createFromFormat(
                    \DateTime::ISO8601,
                    $message->Items->Message[0]->DateTimeReceived
                )->setTimezone('Europe/Kiev');
                $sender = $message->Items->Message[0]->Sender->Mailbox->Name;
                $email = $message->Items->Message[0]->Sender->Mailbox->EmailAddress;
                $folderId = $message->Items->Message[0]->ParentFolderId->Id;

                $headers = array_reverse($message->Items->Message[0]->InternetMessageHeaders->InternetMessageHeader);
                $ipAddressFromMessage = null;
                $dataFromIpApi = [];
                foreach ($headers as $header) {
                    if ($header->HeaderName == 'X-Originating-IP') {
                        $ipAddressFromMessage = Str::substr($header->_, 1, -1);
                        $dataFromIpApi = $this->getIpAddressInfo($ipAddressFromMessage);
                        break;
                    }
                }
                //dump($dataFromIpApi);
                $dataForDb[] = [
                    'date_received' => $dateReceived,
                    'sender'        => $sender,
                    'email'         => $email,
                    'ip_address'    => $ipAddressFromMessage ?? null,
                    'city'          => (isset($dataFromIpApi['city'])) ? (($dataFromIpApi['city'] == "") ? null : $dataFromIpApi['city']) : null,
                    'country_code'  => (isset($dataFromIpApi['countryCode'])) ? (($dataFromIpApi['countryCode'] == "") ? null : strtolower($dataFromIpApi['countryCode'])) : null,
                    'country'       => (isset($dataFromIpApi['country'])) ? (($dataFromIpApi['country'] == "") ? null : $dataFromIpApi['country']) : null,
                    'isp'           => (isset($dataFromIpApi['isp'])) ? (($dataFromIpApi['isp'] == "") ? null : $dataFromIpApi['isp']) : null,
                    'mobile'        => (isset($dataFromIpApi['mobile'])) ? (($dataFromIpApi['mobile'] == "") ? false : true) : false,
                    'org'           => (isset($dataFromIpApi['org'])) ? (($dataFromIpApi['org'] == "") ? null : $dataFromIpApi['org']) : null,
                    'region_name'   => (isset($dataFromIpApi['regionName'])) ? (($dataFromIpApi['regionName'] == "") ? null : $dataFromIpApi['regionName']) : null,
                    'folder_name'   => $folder['name'],
                    'folder_id'     => $folderId,
                ];
            } else {
                //dd($message);
            }
        }

        //dd($dataForDb);
        return $dataForDb;
    }

    /**
     * @param array $dataFromDB
     * @return bool
     */
    public function insertToDatabase(array $dataFromDB): bool
    {
        if (isset($dataFromDB)) {
            if (count($dataFromDB) > 0) {
                try {
                    Message::insert($dataFromDB);
                } catch (Exception $e) {
                    dd($e->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * @param array $foldersFromEnv
     * @return array
     */
    public function getFoldersFromExchange(array $foldersFromEnv): array
    {
        if (($foldersFromEnv[0]) == "") {
            dd('Check environment variable MS_EXCHANGE_PARSE_FOLDERS in .env file');
        }

        $request = new FindFolderType();
        $request->FolderShape = new FolderResponseShapeType();
        $request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->Restriction = new RestrictionType();

        // Search recursively.
        $request->Traversal = FolderQueryTraversalType::DEEP;

        // Search within the root folder. Combined with the traversal set above, this
        // should search through all folders in the user's mailbox.
        $parent = new DistinguishedFolderIdType();
        $parent->Id = DistinguishedFolderIdNameType::ROOT;
        $request->ParentFolderIds->DistinguishedFolderId[] = $parent;

        // Build the restriction that will search for folders containing "Cal".
        $contains = new ContainsExpressionType();
        $contains->FieldURI = new PathToUnindexedFieldType();
        $contains->FieldURI->FieldURI = UnindexedFieldURIType::FOLDER_DISPLAY_NAME;
        $contains->Constant = new ConstantValueType();

        $contains->ContainmentComparison = ContainmentComparisonType::EXACT;
        $contains->ContainmentMode = ContainmentModeType::SUBSTRING;
        $i = 0;
        foreach ($foldersFromEnv as $item) {
            $contains->Constant->Value = $item;
            $request->Restriction->Contains = $contains;

            try {
                $response = $this->client->FindFolder($request);
                $totalItems = $response->ResponseMessages->FindFolderResponseMessage[0]->RootFolder->TotalItemsInView;
                // проверка на существование папки, которая указана в конфиге
                if ($totalItems != 1) {
                    throw new \DomainException(' Folder ' . $item . ' missing on exchange server. Check variable MS_EXCHANGE_PARSE_FOLDERS in .env file');
                }

                if ($item != $response->ResponseMessages->FindFolderResponseMessage[0]->RootFolder->Folders->Folder[0]->DisplayName) {
                    throw new \DomainException(' Folder ' . $item . ' missing on exchange server. Check variable MS_EXCHANGE_PARSE_FOLDERS in .env file');
                }
                $foldersFromExchange[$i]['name'] = $response
                    ->ResponseMessages
                    ->FindFolderResponseMessage[0]
                    ->RootFolder
                    ->Folders
                    ->Folder[0]
                    ->DisplayName;
                $foldersFromExchange[$i]['folder_id'] = $response
                    ->ResponseMessages
                    ->FindFolderResponseMessage[0]
                    ->RootFolder->Folders
                    ->Folder[0]
                    ->FolderId
                    ->Id;
            } catch (Exception $e) {
                dd("Error - " . $e->getMessage() . ', line - ' . $e->getLine() . ' File - ' . $e->getFile());
            }
            $i++;
        }
        return $foldersFromExchange;
    }

    private function getIpAddressInfo($ipAddressFromMessage): array
    {
        $client = new GuzzleClient();
        $request = "http://ip-api.com/json/$ipAddressFromMessage?fields=status,country,countryCode,region,regionName,city,isp,org,mobile,query&lang=ru";

        try {
            $response = $client->get($request)->getBody()->getContents();
            $json = json_decode($response, true);
            if ($json['status'] != 'success') {
                dd("return status != success  -" . $request);
            }
        } catch (Exception $e) {
            dd("Error - " . $e->getMessage() . ', line - ' . $e->getLine() . ' File - ' . $e->getFile());
        }

        return json_decode($response, true);
    }
}
