<?php

namespace App\Actions\Journal;

use App\Models\Setting;
use Insane\Journal\Models\Invoice\DocumentType;

class CreateTeamSettings {
    public function create($teamId) {
    $documentTypes = [[
        "name" => "invoice",
        "label" => "Invoice",
        "subTypes" => [
            ["INVOICE_B01", "Factura de Crédito Fiscal"],
            ["INVOICE_B02", "Factura de Consumo"],
            ["INVOICE_B14", "Regimenes Especiales"],
            ["INVOICE_B15", "Factura Gubernamental"],
            ["INVOICE_B16", "Comprobante para exportaciones"],
            ["INVOICE_NO_NFC", "Factura sin NCF"],
            ["INVOICE_E31", "Factura de Credito fiscal Electronica"],
            ["INVOICE_E32", "Factura de Consumo fiscal Electronica"],
            ["INVOICE_E44", "Regimenes Especiales Electronica"],
            ["INVOICE_E45", "Gubernamental Electronica"],
            ["INVOICE_E47", "Comprobante para exportaciones Electronica"],
        ]
    ], [
        "name" => "credit_note",
        "label" => "Credit Note",
        "subTypes" => [
            ["CREDIT_NOTE_B04", "Nota de credito"],
            ["CREDIT_NOTE_NO_NCF", "Nota de credito sin NCF"],
            ["CREDIT_NOTE_E34", "Nota de credito  Electronica"],
        ]
    ], [
        "name" => "debit_note",
        "label" => "Debit Note",
        "subTypes" => [
            ["DEBIT_NOTE_B03", "Nota de debito"],
            ["DEBIT_NOTE_NO_NCF", "Nota de debito sin NCF"],
            ["DEBIT_NOTE_E33", "Nota de debito  Electronica"],
        ]
    ]];

    foreach ($documentTypes as $documentType) {
        $document = DocumentType::create([
            "name" => $documentType["name"],
            "label" => $documentType["label"],
            "team_id" => $teamId,
        ]);
        foreach ($documentType['subTypes'] as $subType) {
            $document->subTypes()->create([
                'name' => $subType[0],
                'label' => $subType[1],
                "team_id" => $document->team_id,
            ]);
        }
    }

    $settings = [
        "regime_general" => "GENERAL_REGIME",
        "regime_rst" => "GENERAL_REGIME",
        "regime_special_taxation" => "SPECIAL_REGIME_OF_TAXATION",
        "idType_tax_id" => "tax_id",
        "idType_id" => "id",
        "idType_ie" => "ie",
        "paymentType_cash" => "CASH",
        "paymentType_check" => "CHECK",
        "paymentType_free" => "FREE",
        "paymentMethod_cash" => "CASH",
        "paymentMethod_check" => "CHECK",
        "paymentMethod_transfer" => "TRANSFER",
        "paymentMethod_deposit" => "DEPOSIT",
        "paymentMethod_debit_card" => "DEBIT CARD",
        "paymentMethod_credit_card" => "CREDIT CARD",
        "paymentMethod_credit" => "CREDIT",
        "paymentMethod_gift" => "GIFT VOUCHER",
        "paymentMethod_mixed" => "MIXED",
    ];

    foreach ($settings as $key => $value) {
        Setting::create([
            'name' => $key,
            'value' => $value,
            'team_id' => $teamId,
        ]);
    }
}
}
