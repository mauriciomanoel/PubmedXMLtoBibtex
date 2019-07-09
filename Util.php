<?php

class Util {

    public static function getURL($url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        //CURLOPT_URL => "https://api.ncbi.nlm.nih.gov/lit/ctxp/v1/pmc/?format=ris&id=5677444",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "Host: api.ncbi.nlm.nih.gov",
            "Postman-Token: 06911a14-33dc-4964-a48f-7035bf069b42,f33861d7-87cb-4efc-84b2-24dac3e8dc03",
            "User-Agent: PostmanRuntime/7.15.0",
            "accept-encoding: gzip, deflate",
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } 
        return $response;        
    }
    public static function loadURL($url, $cookie, $user_agent, $fields=array(), $parameters=array()) 
    {        
        $ch 		= curl_init($url);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        if (empty($fields) && count($fields) ==0) {
            curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
        } else {
            $fields_string = "";
            foreach($fields as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        }
        
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3";
        $header[] = "Cache-Control: no-cache";
        $header[] = "Connection: keep-alive";
        $header[] = "Host: api.ncbi.nlm.nih.gov";
        $header[] = "Accept-Charset: UTF-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3";
        $header[] = "Upgrade-Insecure-Requests: 1";
        $header[] = "accept-encoding: gzip, deflate";

        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        if (!empty($parameters["referer"])) {
            curl_setopt( $ch, CURLOPT_REFERER, $parameters["referer"]);
        }
        curl_setopt( $ch, CURLOPT_ENCODING, "gzip, deflate, br");
        curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent);
        $output 	= curl_exec($ch);
        var_dump($output); exit;
        curl_close( $ch );
        return $output;
    }

    public static function slug($string, $replacement = '_') 
    {
        $transliteration = array(
            '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
            '/Æ|Ǽ/' => 'AE',
            '/Ä/' => 'Ae',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/Ð|Ď|Đ/' => 'D',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
            '/Ĝ|Ğ|Ġ|Ģ|Ґ/' => 'G',
            '/Ĥ|Ħ/' => 'H',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І/' => 'I',
            '/Ĳ/' => 'IJ',
            '/Ĵ/' => 'J',
            '/Ķ/' => 'K',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
            '/Ñ|Ń|Ņ|Ň/' => 'N',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
            '/Œ/' => 'OE',
            '/Ö/' => 'Oe',
            '/Ŕ|Ŗ|Ř/' => 'R',
            '/Ś|Ŝ|Ş|Ș|Š/' => 'S',
            '/ẞ/' => 'SS',
            '/Ţ|Ț|Ť|Ŧ/' => 'T',
            '/Þ/' => 'TH',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
            '/Ü/' => 'Ue',
            '/Ŵ/' => 'W',
            '/Ý|Ÿ|Ŷ/' => 'Y',
            '/Є/' => 'Ye',
            '/Ї/' => 'Yi',
            '/Ź|Ż|Ž/' => 'Z',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
            '/ä|æ|ǽ/' => 'ae',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/ð|ď|đ/' => 'd',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
            '/ƒ/' => 'f',
            '/ĝ|ğ|ġ|ģ|ґ/' => 'g',
            '/ĥ|ħ/' => 'h',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і/' => 'i',
            '/ĳ/' => 'ij',
            '/ĵ/' => 'j',
            '/ķ/' => 'k',
            '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
            '/ñ|ń|ņ|ň|ŉ/' => 'n',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
            '/ö|œ/' => 'oe',
            '/ŕ|ŗ|ř/' => 'r',
            '/ś|ŝ|ş|ș|š|ſ/' => 's',
            '/ß/' => 'ss',
            '/ţ|ț|ť|ŧ/' => 't',
            '/þ/' => 'th',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
            '/ü/' => 'ue',
            '/ŵ/' => 'w',
            '/ý|ÿ|ŷ/' => 'y',
            '/є/' => 'ye',
            '/ї/' => 'yi',
            '/ź|ż|ž/' => 'z',
        );
        
        $quotedReplacement = preg_quote($replacement, '/');

        $merge = array(
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $replacement,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );

        $map = $transliteration + $merge;
        return preg_replace(array_keys($map), array_values($map), $string);
    }

    public static function getCookie($url) 
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt( $ch, CURLOPT_HTTPGET, 1 ); 
        // get headers too with this line
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        // get cookie
        // multi-cookie variant contributed by @Combuster in comments
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        $cookie = "";
        foreach($cookies as $key => $value) {
            $cookie .= $key . "=" . $value . ", ";
        }
        $cookie = rtrim($cookie, ", ");
        return $cookie;
    }

    public static function getHTMLFromClass($html, $classname, $element="*") {

        $dom = self::getDOM($html);
        $finder = new DomXPath($dom);
        $nodes = $finder->query("//" . $element . "[contains(@class, '$classname')]");
        $values = array();
        
        foreach($nodes as $node) {
            $values[] = $dom->saveHTML($node);
        }
        
        return $values;
    }

    public static function getAttributeFromClass($html, $classname, $attibutename) {

        $dom = self::getDOM($html);
        $finder = new DomXPath($dom);
        $nodes = $finder->query("//*[contains(@class, '$classname')]");
        $values = array();
        
        foreach($nodes as $node) {
            $values[] = trim($node->getAttribute($attibutename));
        }
        
        return $values;
    }

    public static function getDOM($value) 
    {
        libxml_use_internal_errors(true) && libxml_clear_errors(); // for html5
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = true;
        
        return $dom;
    }

    public static function getURLFromHTML($html) {
        preg_match_all('/href="([^"]+)"/', $html, $arr, PREG_PATTERN_ORDER);
        if (!empty($arr[1])) {
            return $arr[1][0];
        }
        return "";
    }

    public static function add_fields_bibtex($bibtex, $data) 
    {
        $bibtex = trim($bibtex);
        $string = "";
        $delimiter = self::getDelimiter($bibtex);
       
        if ($delimiter == "{") {
            foreach($data as $key => $value) {
                $string .= "" . $key . "={" . $value . "}," . "\n";
            }
            $string = rtrim(trim($string), ",");
            $string .= "\n";
        } else {

            foreach($data as $key => $value) {
                $string .= "" . $key . "=" . $delimiter . $value . $delimiter . "," . "\n";
            }            
            $string = rtrim(trim($string), ",");
            $string .= "\n";

        }
        $bibtex = trim(substr($bibtex, 0, -1));        
        if ( substr($bibtex, strlen($bibtex)-1) == ",") {
            $bibtex .= "\n";
        } else {
            $bibtex .= ",\n";
        }
        $bibtex .= "" . $string;
        $bibtex .= "}\n";

        return $bibtex;
    }

    public static function arrayToString($value) {
        return implode(" ", $value);
    }

	public static function getDelimiter($string)
	{
        $string = trim($string);
        $string = str_replace(array(" ","\n"), "", $string);
        $position = strpos($string, "=");
        return substr($string, ($position+1), 1);
    }
    
    public static function showMessage($message) {
        while (@ ob_end_flush()); // end all output buffers if any            
            echo $message . BREAK_LINE;
        @flush();  

        ob_start();
            echo $message . "\r\n";
            $log = ob_get_contents();
            file_put_contents(FILE_LOG, $log, FILE_APPEND);
        @ob_end_clean();            
    }
}
?>