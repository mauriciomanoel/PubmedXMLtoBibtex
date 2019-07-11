<?php
    set_time_limit(0);

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });
    
    function createBibtex($values = array()) {

        Util::showMessage("Total Works: " . count($values));
        $name = "pubmed.bib";
        // use echo for testing purposes only
        // cause echo considered as a content of your file
        $bibtex = "";
        foreach($values as $value) {

            $type = "article";
             
            $dates = $value["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"];
            $month = (!empty($dates["Month"])) ? $dates["Month"] : "";
            $year = (!empty($dates["Year"])) ? $dates["Year"] : "";
            
            $title = $value["MedlineCitation"]["Article"]["ArticleTitle"];
            $abstract = $value["MedlineCitation"]["Article"]["Abstract"]["AbstractText"];

            if (is_array($abstract)) {
                $abstract = $abstract[0];
            }           
                     
			$valueDoi = @$value["MedlineCitation"]["Article"]["ELocationID"];
            if (!empty($valueDoi) && is_array($valueDoi)) {
                $doi = "https://doi.org/" . $valueDoi[0];
            } else {
                $doi = (!empty($valueDoi)) ? "https://doi.org/" . $valueDoi : "";
            }
		        
            $link = "https://www.ncbi.nlm.nih.gov/pubmed/" . $value["MedlineCitation"]["PMID"];
            
            $autores = $value["MedlineCitation"]["Article"]["AuthorList"]["Author"];
            $temp = "";            
            if (empty(@$autores["LastName"])) {                
                foreach($autores as $autor) {                
                    $temp .= $autor["LastName"] . " " . $autor["ForeName"] . ", ";
                }
                $autor = trim($temp, ", ");
            } else {
                
                $autor = $autores["LastName"] . " " . $autores["ForeName"]; 
            }

            $keyword = "";
            if (!empty($value["MedlineCitation"]["KeywordList"])) {
                $keywords = $value["MedlineCitation"]["KeywordList"]["Keyword"];
                $temp = "";
                foreach($keywords as $keyword) {
                    $temp .= $keyword . ", ";
                }
                $keyword = trim($temp, ", ");
            }                

            $abstract = str_replace(PHP_EOL, ' ', (!empty( $abstract )) ? $abstract : "");

            $bibtex .= "@$type{" . $value["MedlineCitation"]["PMID"] . ",
                source={PubMed},
                author={" . $autor . "},
                title={" . $title . "},
                month={" . $month . "},
                year={" . $year . "},
                abstract={" . $abstract . "},
                keywords={" . $keyword . "},
                doi={" . $doi . "},
                url={" . $link . "}
            }\n";
        }

        $fp = fopen($name, 'a');
        fwrite($fp, $bibtex);
        fclose($fp);   // don't forget to close file for saving newly added data
        Util::showMessage("Saved file: $name");
    }

    $break_line         = "<br>";
    
    define('BREAK_LINE', $break_line);
    
    $xmlstring  = file_get_contents("pubmed_result.xml");
    $xml        = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json       = json_encode($xml);    
    $array      = json_decode($json,TRUE);
    $articles   = $array["PubmedArticle"];

    createBibtex($articles);
?>
