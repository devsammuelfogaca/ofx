<?php

namespace FogacaSammuel\Ofx;

use DOMDocument;

class Ofx 
{
    /** @var array */
    private array $properties;

    /** @var DOMDocument */
    private $dom;

    /** @var string */
    private $xml;

    /** @var string */
    private $filepath;

    /**
     * Initialize Class
     *
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->properties = [];
        $this->xml = "";

        if(!file_exists($this->filepath))
            throw new \Exception('Path of OFX file doesn\'t exists.');

        $this->dom = new DOMDocument();
        $this->workOfx();
    }

    /**
     * Work the ofx file to get properties and XML
     *
     * @return void
     */
    private function workOfx() : void
    {
        $ofxLine = -1;
        $file = fopen($this->filepath, 'r');

        while(!feof($file)){
            $line = trim(fgets($file));

            if(preg_match('/^<OFX>$/', $line))
                $ofxLine = 1;

            if($ofxLine == -1 && preg_match('/(?!\<)(.*\:)/', $line)){
                $attr = explode(':', $line);
                $this->properties[$attr[0]] = $attr[1] ?? null;
                continue;
            }

            $this->xml .= $line;
        }

        fclose($file);
        $this->dom->loadXML($this->xml);
    }

    /**
     * Convert data string to Date
     *
     * @param string $date
     * @param string $format
     * @return void
     */
    private function strToDate(string $date): string
    {

        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);

        $newDate = $year . "-" . $month . "-" . $day;
        return $newDate;
    }

    /**
     * Returns all properties
     *
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * Get informations from signonmsgsrsv1
     *
     * @return array
     */
    private function signonmsgsrsv1() : array
    {
        $sonrs = $this->dom->documentElement
                    ->getElementsByTagName('SIGNONMSGSRSV1')
                    ->item(0)
                    ->getElementsByTagName('SONRS')
                    ->item(0);

        return [
            'status_code' => $sonrs->getElementsByTagName('STATUS')->item(0)->getElementsByTagName('CODE')->item(0)->nodeValue,
            'status_severity' => $sonrs->getElementsByTagName('STATUS')->item(0)->getElementsByTagName('SEVERITY')->item(0)->nodeValue,
            'dtserver' => $this->strToDate($sonrs->getElementsByTagName('DTSERVER')->item(0)->nodeValue),
            'language' => $sonrs->getElementsByTagName('LANGUAGE')->item(0)->nodeValue,
            'fi_org' => $sonrs->getElementsByTagName('FI')->item(0)->getElementsByTagName('ORG')->item(0)->nodeValue,
            'fi_fid' => $sonrs->getElementsByTagName('FI')->item(0)->getElementsByTagName('FID')->item(0)->nodeValue,
        ];
    }

    /**
     * Get information from stmttrnrs
     *
     * @return array
     */
    private function stmttrnrs() : array
    {
        $stmttrnrs = $this->dom->documentElement->getElementsByTagName('BANKMSGSRSV1')->item(0)->getElementsByTagName('STMTTRNRS')->item(0);

        return [
            'trnuid' => $stmttrnrs->getElementsByTagName('TRNUID')->item(0)->nodeValue,
            'status_code' => $stmttrnrs->getElementsByTagName('STATUS')->item(0)->getElementsByTagName('CODE')->item(0)->nodeValue,
            'status_severity' => $stmttrnrs->getElementsByTagName('STATUS')->item(0)->getElementsByTagName('SEVERITY')->item(0)->nodeValue,
        ];
    }

    /**
     * Return information about account
     *
     * @return array
     */
    public function account() : array
    {
        $stmtrs = $this->dom->documentElement->getElementsByTagName('BANKMSGSRSV1')->item(0)->getElementsByTagName('STMTTRNRS')->item(0)->getElementsByTagName('STMTRS')->item(0);

        return [
            'currency' => $stmtrs->getElementsByTagName('CURDEF')->item(0)->nodeValue,
            'bank_code' => $stmtrs->getElementsByTagName('BANKACCTFROM')->item(0)->getElementsByTagName('BANKID')->item(0)->nodeValue,
            'agency' => $stmtrs->getElementsByTagName('BANKACCTFROM')->item(0)->getElementsByTagName('BRANCHID')->item(0)->nodeValue,
            'account' => $stmtrs->getElementsByTagName('BANKACCTFROM')->item(0)->getElementsByTagName('ACCTID')->item(0)->nodeValue,
            'type' => $stmtrs->getElementsByTagName('BANKACCTFROM')->item(0)->getElementsByTagName('ACCTTYPE')->item(0)->nodeValue
        ];
    }

    /**
     * Return data of financial invoices
     *
     * @return array
     */
    public function invoices() : array
    {
        $banktranlist = $this->dom->documentElement
                        ->getElementsByTagName('BANKMSGSRSV1')->item(0)
                        ->getElementsByTagName('STMTTRNRS')->item(0)
                        ->getElementsByTagName('STMTRS')->item(0)
                        ->getElementsByTagName('BANKTRANLIST')->item(0);

        $invoices = [];
        foreach($banktranlist->getElementsByTagName('STMTTRN') as $stmttrn){
            $invoices[] = [
                'type' => $stmttrn->getElementsByTagName('TRNTYPE')->item(0)->nodeValue,
                'due_at' => $this->strToDate($stmttrn->getElementsByTagName('DTPOSTED')->item(0)->nodeValue),
                'value' => (float) $stmttrn->getElementsByTagName('TRNAMT')->item(0)->nodeValue,
                'transaction_id' => $stmttrn->getElementsByTagName('FITID')->item(0)->nodeValue,
                // 'checknum' => $stmttrn->getElementsByTagName('CHECKNUM')->item(0)->nodeValue,
                // 'refnum' => $stmttrn->getElementsByTagName('REFNUM')->item(0)->nodeValue,
                'content' => $stmttrn->getElementsByTagName('MEMO')->item(0)->nodeValue,
            ];
        }

        return [
            'date_start' => $banktranlist->getElementsByTagName('DTSTART')->item(0)->nodeValue,
            'date_end' => $banktranlist->getElementsByTagName('DTEND')->item(0)->nodeValue,
            'invoices' => $invoices
        ];
    }

    /**
     * Return data of balance
     *
     * @return array
     */
    public function balance() : array
    {
        $ledgerbal = $this->dom->documentElement
                        ->getElementsByTagName('BANKMSGSRSV1')->item(0)
                        ->getElementsByTagName('STMTTRNRS')->item(0)
                        ->getElementsByTagName('STMTRS')->item(0)
                        ->getElementsByTagName('LEDGERBAL')->item(0);

        return [
            'total' => (float) $ledgerbal->getElementsByTagName('BALAMT')->item(0)->nodeValue,
            'date_end' => $this->strToDate($ledgerbal->getElementsByTagName('DTASOF')->item(0)->nodeValue)
        ];
    }
 }
?>