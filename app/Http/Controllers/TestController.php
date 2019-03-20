<?php

namespace App\Http\Controllers;


use App\Entity\Email\Message;
use App\Services\Parser\ParseMailBox;
use DateTime;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfFieldOrdersType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfPathsToElementType;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Enumeration\BodyTypeResponseType;
use jamesiarmes\PhpEws\Enumeration\ContainmentComparisonType;
use jamesiarmes\PhpEws\Enumeration\ContainmentModeType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType;
use jamesiarmes\PhpEws\Enumeration\ItemQueryTraversalType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Enumeration\UserConfigurationPropertyType;
use jamesiarmes\PhpEws\Request\FindFolderType;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\Request\GetFolderType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Request\GetUserConfigurationType;
use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\ContactsViewType;
use jamesiarmes\PhpEws\Type\ContainsExpressionType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\FieldOrderType;
use jamesiarmes\PhpEws\Type\FolderResponseShapeType;
use jamesiarmes\PhpEws\Type\IndexedPageViewType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\RestrictionType;
use jamesiarmes\PhpEws\Type\UserConfigurationNameType;

class TestController extends Controller
{

    private $service;
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client, ParseMailBox $service)
    {
        $this->service = $service;

        $this->client = $client;
        $this->client->setCurlOptions([CURLOPT_SSL_VERIFYPEER => false]);
    }

    public function test()
    {
        $request_folder_inbox = new GetFolderType();
        $request_folder_inbox->FolderShape = new FolderResponseShapeType();
        $request_folder_inbox->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request_folder_inbox->FolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request_folder_inbox->FolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
        $request_folder_inbox->FolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;
        $response_folder_inbox = $this->client->GetFolder($request_folder_inbox);

// Кол-во писем в INBOX
        dd($response_folder_inbox->ResponseMessages->GetFolderResponseMessage[0]->Folders->Folder[0]->TotalCount);

        $request = new FindItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;
        $request->Traversal = ItemQueryTraversalType::SHALLOW;

// Извлекаем объекты из папки INBOX
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;

        // sort order
        $request->SortOrder = new NonEmptyArrayOfFieldOrdersType();
        $request->SortOrder->FieldOrder = array();
        $order = new FieldOrderType();

        // sorts mails so that oldest appear first
// more field uri definitions can be found from types.xsd (look for UnindexedFieldURIType)
        $order->FieldURI = '';
        @$order->FieldURI->FieldURI = 'item:DateTimeReceived'; // @ symbol stops the creating default object from empty value error
        $order->Order = 'Ascending';
        $request->SortOrder->FieldOrder[] = $order;


        // Build the request for the parts.
        $parts_request = new GetItemType();
        $parts_request->ItemShape = new ItemResponseShapeType();
        $parts_request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;
// You can get the body as HTML, text or "best".
        $parts_request->ItemShape->BodyType = BodyTypeResponseType::TEXT;

// Add the body property.
        $body_property = new PathToUnindexedFieldType();
        $body_property->FieldURI = 'item:InternetMessageHeaders';
        $body_property2 = new PathToUnindexedFieldType();
        $body_property2->FieldURI = 'message:Sender';
        $body_property3 = new PathToUnindexedFieldType();
        $body_property3->FieldURI = 'item:DateTimeSent';

        $parts_request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();
        $parts_request->ItemShape->AdditionalProperties->FieldURI = array($body_property, $body_property2, $body_property3);


        $parts_request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $parts_request->ItemIds->ItemId = array();

        // // Limits the number of items retrieved
        $request->IndexedPageItemView = new IndexedPageViewType();
        $request->IndexedPageItemView->BasePoint = "Beginning";

        $request->IndexedPageItemView->Offset = 5696;
        $request->IndexedPageItemView->MaxEntriesReturned = 2;
        $response = $this->client->FindItem($request);
        dump($response);

        $message_item = new ItemIdType();
        $message_item->Id = 'AAMkADNlYzgwNjdlLTlkYTAtNDMwZS1hM2ExLWQ5MTA0ZGY2ZTg1MgBGAAAAAACZBt/pqgF5TrHJm9t+qhHYBwBDI2zCSD4pTK46Kx7koBULAAAAallkAABDI2zCSD4pTK46Kx7koBULAAAEb4iKAAA=';
        $parts_request->ItemIds->ItemId[] = $message_item;
        $messages_response = $this->client->GetItem($parts_request);
        dd($messages_response->ResponseMessages->GetItemResponseMessage[0]->Items->Message[0]);
    }

    public function test2()
    {
//        dd(config('mail.ms_exchange_parse_folders'));
// Build the request.
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
        $contains->Constant->Value = 'test';
        $contains->ContainmentComparison = ContainmentComparisonType::EXACT;
        $contains->ContainmentMode = ContainmentModeType::SUBSTRING;
        $request->Restriction->Contains = $contains;

        $response = $this->client->FindFolder($request);
        dd($response);
    }

    public function test3()
    {
        // Build the request for the parts.
        $parts_request = new GetItemType();
        $parts_request->ItemShape = new ItemResponseShapeType();
        $parts_request->ItemShape->BaseShape = DefaultShapeNamesType::ID_ONLY;
// You can get the body as HTML, text or "best".
        $parts_request->ItemShape->BodyType = BodyTypeResponseType::TEXT;

// Add the body property.
        $body_property = new PathToUnindexedFieldType();
        $body_property->FieldURI = 'item:InternetMessageHeaders';
        $body_property2 = new PathToUnindexedFieldType();
        $body_property2->FieldURI = 'message:Sender';
        $body_property3 = new PathToUnindexedFieldType();
        $body_property3->FieldURI = 'item:DateTimeSent';

        $parts_request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();
        $parts_request->ItemShape->AdditionalProperties->FieldURI = array($body_property, $body_property2, $body_property3);


        $parts_request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $parts_request->ItemIds->ItemId = array();


        $message_item = new ItemIdType();
        $message_item->Id = 'AAMkADNlYzgwNjdlLTlkYTAtNDMwZS1hM2ExLWQ5MTA0ZGY2ZTg1MgBGAAAAAACZBt/pqgF5TrHJm9t+qhHYBwA+pQDcHh6pQL9xIJpICPvHAFuIrIBZAAA+pQDcHh6pQL9xIJpICPvHAFuIrJ4BAAA=';
        $parts_request->ItemIds->ItemId[] = $message_item;
        $messages_response = $this->client->GetItem($parts_request);
        dd($messages_response->ResponseMessages->GetItemResponseMessage[0]->Items->Message[0]);


    }

}
