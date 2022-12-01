<?php

namespace App\Http\Controllers;

use App\Domains\Loans\Actions\UpdateLatePayments;

class BackgroundController extends Controller
{
    public function __invoke()
    {
        $this->backgroundPost(config('app.url') . '/background/update-late-payments');
    }

    public function updateLatePayments() {
        UpdateLatePayments::run();
        return "ran";
    }

    private function backgroundPost(string $url) {
        $parts = parse_url($url);

        $fp = fsockopen($parts['host'],
                isset($parts['port'])?$parts['port']:80,
                $errno, $errstr, 30);
    
        if (!$fp) {
            return false;
        } else {
            $out = "POST ".$parts['path']." HTTP/1.1\r\n";
            $out.= "Host: ".$parts['host']."\r\n";
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out.= "Connection: Close\r\n\r\n";
            if (isset($parts['query'])) $out.= $parts['query'];
    
            fwrite($fp, $out);
            fclose($fp);
            return true;
        }   
    }
}
