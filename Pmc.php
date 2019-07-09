<?php

class Pmc {

    private static $URL = 'https://www.sciencedirect.com';

    public static function getURL($offset, $query) 
    {
        $url = self::$URL . "/search/advanced?tak=$query&show=100&sortBy=relevance&articleTypes=FLA%2CCH&offset=$offset";
        Util::showMessage($url);
        return $url;
    }

    public static function start($page, $query_string, $url) {
        Util::showMessage("Page: " . $page);
        $url = self::getUrl($page, $query_string);
        self::progress($url);
    }

    public static function progress($url) {        
        $html = Util::loadURL($url, COOKIE, USER_AGENT);
        
        // Check Google Captcha
        if ( strpos($html, "gs_captcha_cb()") !== false || strpos($html, "sending automated queries") !== false ) {
            Util::showMessage("Captha detected"); exit;
        }

        //var_dump($html); exit;
        preg_match_all('/INITIAL_STATE =(.*)/', $html, $values, PREG_PATTERN_ORDER);

        if (!empty($values) ) {
            $value = $values[1][0];
            $value = trim($value);
            $value = str_replace("</script>", "", $value);
            $jsonValue = json_decode($value, true);
        }

        $bibtex_new = "";
        $articles = $jsonValue["search"]["searchResults"];
        foreach($articles as $article) {
           
            $data     = self::getDataArticle($article);
            Util::showMessage($article["title"]);
            $bibtex      = self::getBibtex($article["pii"]);
            $bibtex_new .= Util::add_fields_bibtex($bibtex, $data);
            sleep(rand(2,4)); // rand between 5 and 8 seconds
        }
        Util::showMessage("Download bibtex file OK.");
        Util::showMessage("");
/*
        $classname="ResultItem col-xs-24 push-m";
        $htmlValues = Util::getHTMLFromClass($html, $classname, "li");        
        $bibtex_new = "";
        $could_not_downloaded = 0;

        var_dump($html); exit;
        foreach($htmlValues as $htmlValue) {

            $data     = self::getDataArticle($htmlValue);            
            Util::showMessage($data["title"]);            
            $bibtex      = self::getBibtex($data["doc"]);
            
            if ( strpos($bibtex, "innerHTML") !== false || 
                 strpos($bibtex, "<body>") !== false || 
                 strpos($bibtex, "function(") !== false ||
                 strpos($bibtex, "gs_captcha_cb()") !== false ||
                 strpos($bibtex, "sending automated queries") !== false ||
                 strpos($bibtex, "<html>") !== false) {
                Util::showMessage("Detected HTML or Captha detected"); exit;
            }
            if (empty( $bibtex)) {
                Util::showMessage("Bibtex could not be downloaded"); 
                $could_not_downloaded++;
                if ($could_not_downloaded > 3) exit;
            } else {
                unset($data["title"]);
                unset($data["doc"]);
                $bibtex_new .= Util::add_fields_bibtex($bibtex, $data);

                var_dump($bibtex_new); exit;
                Util::showMessage("Download bibtex file OK.");
                Util::showMessage("");
            }

            sleep(rand(2,4)); // rand between 5 and 8 seconds            
        }
*/
        if (!empty($bibtex_new)) {
            file_put_contents(FILE, $bibtex_new, FILE_APPEND);
            Util::showMessage("File " . FILE . " saved successfully.");
            Util::showMessage("");
        }
    }

    public static function getDataArticle($value) {
        $retorno        = array("url_article"=>"", "title"=> "", "doc"=>"", "link_pdf"=>"");

                          
        $title          = $value["sourceTitle"];
        $link_pdf       = self::$URL . $value["pdf"]["downloadLink"];
        $url_article    = self::$URL . $value["link"];

       
        $retorno["doc"] = $value["pii"];
        $retorno["title"] = $title;
        $retorno["url_article"] = $url_article;
        $retorno["link_pdf"] = $link_pdf;
        

        return $retorno;
    }
        
    public static function getBibtex($doc) {
        $url = self::$URL . "/sdfe/arp/cite?pii=$doc&format=text%2Fx-bibtex&withabstract=true";
        
        $bibtex = Util::loadURL($url, COOKIE, USER_AGENT);
        $bibtex = strip_tags($bibtex); // remove html tags 
        return $bibtex;        
    }
    
    public static function getPDF($file) {
        $pdf = Util::loadURL($file, COOKIE, USER_AGENT);
        return $pdf;
    }
}
    
?>
