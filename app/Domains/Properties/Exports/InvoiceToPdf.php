<?php 

namespace App\Domains\Properties\Exports;

use Insane\Journal\Models\Invoice\Invoice;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;

class InvoiceToPdf {
  private $processor = null;

  public function process(Invoice $invoice) {
    $doc = new TemplateProcessor('C:\laragon\www\prestapp\public\templates\simple_rent_invoice.docx');
    $doc->setValues($invoice->toArray());
    $doc->setValue('client', $invoice->client);
    $items = $invoice->lines->map(function($line) {
      return [
        "itemDescription" => $line->concept,
        "itemPrice" => $line->price ?? 0,
        "itemComission" => $line->discount ?? 0,
        "itemSubtotal" => $line->amount,
        "itemTotal" => $line->amount,
      ];
    });
    $doc->cloneRowAndSetValues('itemDescription', $items);
    // $filename = Str::slug("$invoice->concept", "_");
    // $doc->saveAs(public_path("$filename.docx"));
    $this->processor = $doc;
  }


  public function previewAs($filename = 'file') {
    $filename = Str::slug($filename, "_");
    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    $filePath = $this->processor->save();
    return file_get_contents($filePath);
  }
}