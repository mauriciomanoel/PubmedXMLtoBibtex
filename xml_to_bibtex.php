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
		
		//
		
        foreach($values as $value) {
			
			//var_dump($value); exit;
			$pmid = (!empty($value["MedlineCitation"])) ? $value["MedlineCitation"]["PMID"] : $value["PMID"];
			if (empty($pmid)) continue;
            $type = "article";
             
            $dates = (!empty($value["MedlineCitation"])) ? $value["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"] : $value["Article"]["Journal"]["JournalIssue"]["PubDate"];
            $month = (!empty($dates["Month"])) ? $dates["Month"] : "";
            $year = (!empty($dates["Year"])) ? $dates["Year"] : "";
            
            $title = (!empty($value["MedlineCitation"])) ? $value["MedlineCitation"]["Article"]["ArticleTitle"] : $value["Article"]["ArticleTitle"];
            $abstract = @$value["MedlineCitation"]["Article"]["Abstract"]["AbstractText"];
						
            if (is_array($abstract)) {
                $abstract = $abstract[0];
            }           
                     
			$valueDoi = (!empty($value["MedlineCitation"])) ? @$value["MedlineCitation"]["Article"]["ELocationID"] : @$value["Article"]["ELocationID"];
            if (!empty($valueDoi) && is_array($valueDoi)) {
                $doi = "https://doi.org/" . $valueDoi[0];
            } else {
                $doi = (!empty($valueDoi)) ? "https://doi.org/" . $valueDoi : "";
            }
		        
			$pmid = (!empty($value["MedlineCitation"])) ? $value["MedlineCitation"]["PMID"] : $value["PMID"];
            $link = "https://www.ncbi.nlm.nih.gov/pubmed/" . $pmid ;
            
            $autores = (!empty($value["MedlineCitation"])) ? $value["MedlineCitation"]["Article"]["AuthorList"]["Author"] : $value["Article"]["AuthorList"]["Author"];
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
			
			if (empty($keyword)) {
				if (!empty($value["MeshHeadingList"])) {
					$keywords = $value["MeshHeadingList"]["MeshHeading"];
					
					$temp = "";
					foreach($keywords as $keyword) {
						$temp .= $keyword["DescriptorName"] . ", ";
					}
					$keyword = trim($temp, ", ");
				}
			}
			
			if (empty($keyword)) {
				if (!empty($value["MedlineCitation"]["MeshHeadingList"])) {
					$keywords = $value["MedlineCitation"]["MeshHeadingList"];
					
					$temp = "";
					foreach($keywords as $keyword) {
						if (empty($keyword["DescriptorName"])) {
							var_dump($keyword); exit;
						}
						
												var_dump($keyword); 
						$temp .= $keyword["DescriptorName"] . ", ";
					}
					$keyword = trim($temp, ", ");
				}
			}
				

			if (empty($keyword)) {
				Util::showMessage($title . " without keywords");
			}
		
            $abstract = str_replace(PHP_EOL, ' ', (!empty( $abstract )) ? $abstract : "");

            $bibtex .= "@$type{" . $pmid . ",
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
