<?php

namespace App\Controllers\PeachtreeIdMap;

use BluelimeSystem;

class PeachtreeIdMap extends \App\Controllers\BaseController
{
    public function map_id() {
        $fromPeachtreeModel = model('PeachtreeIdMap/FromPeachtreeModel');
        $inventoryAllModel = model('PeachtreeIdMap/InventoryAllModel');

        $peachtree_items = $fromPeachtreeModel->where('quantity_on_hand !=',0)->where('bluelime_id_full',null)->findAll();
        $inventory_items = $inventoryAllModel->where('remark', null)->findAll();

        $MATCH_FIRST_LETTER = $_GET['match_frist_letter'] ?? 3;

        $inventory_items_by_location = [];
        foreach($inventory_items as $inventory_item){
            if(!isset($inventory_items_by_location[$inventory_item['location']]))
                $inventory_items_by_location[$inventory_item['location']] = [];
            $inventory_items_by_location[$inventory_item['location']][] = $inventory_item;
        }

        //BluelimeSystem::print_associated_array($inventory_items_by_location);


        $cnt = 0;
        $collection = [];
        foreach($peachtree_items as $index =>$peachtree_item){
            $peachtree_items[$index]['item_description'] = trim(substr($peachtree_item['item_description'],0,strlen($peachtree_item['item_description'])-3));
            $peachtree_item = $peachtree_items[$index];

            if(!isset($peachtree_item['chuncked_name'])){
                $peachtree_items[$index]['chuncked_name'] = $this->chunk_name($peachtree_item['item_description']);
                $peachtree_item = $peachtree_items[$index];
            }
            
            $peachtree_items[$index]['similarity'] = ['avarage' => 0, 'bluelime_data' => []];
            $found = false;
            foreach(($inventory_items_by_location[$peachtree_item['location']]??[]) as $b_index => $bluelime_item){
                if(strtolower(substr($peachtree_item['item_description'],0,$MATCH_FIRST_LETTER)) != strtolower(substr($bluelime_item['peachtree_discription'],0,$MATCH_FIRST_LETTER)))
                    continue;

                
                if(!isset($bluelime_item['chuncked_name'])){
                    //$bluelime_items[$b_index]['chuncked_name'] = $this->chunk_name($bluelime_item['peachtree_discription']);
                    $bluelime_item['chuncked_name'] = $this->chunk_name($bluelime_item['peachtree_discription']);
                }

                [$avarage,$word_percent, $number_percent, $smilarity_val] = $this->calculate_string_similarity($peachtree_item['chuncked_name'], $bluelime_item['chuncked_name']);
                if($avarage > $peachtree_items[$index]['similarity']['avarage']){
                    $peachtree_items[$index]['similarity'] = [
                            'bluelime_data' => $bluelime_item,
                            'avarage' => $avarage
                        ];
                        $peachtree_items[$index]['avarage'] = $avarage;
                        $peachtree_items[$index]['word_percent'] = $word_percent;
                        $peachtree_items[$index]['number_percent'] = $number_percent;
                        $peachtree_items[$index]['smilarity_val'] = $smilarity_val;
                }
                $found = true;
                //print_r([$avarage,$word_percent, $number_percent, $smilarity_val]);
                //die();
            }
            if($found)
                if($peachtree_items[$index]['smilarity_val'] >= 20)
                    $collection[] = $peachtree_items[$index];

            if(++$cnt >= 1000){
                break;//break;
            }
        }

        $collection = BluelimeSystem::sort_array($collection, ['avarage'], true);

        //BluelimeSystem::print_associated_array($collection);

        $view_data = [
            'peachtree_items' => $collection,
            'MATCH_FIRST_LETTER' => $MATCH_FIRST_LETTER
        ];

        return view('PeachtreeIdMap/similarity_map', $view_data);

    }

    public function map_id2(){
        $fromPeachtreeModel = model('PeachtreeIdMap/FromPeachtreeModel');
        $bluelimeItemModel = model('PeachtreeIdMap/BluelimeItemModel');
        
        $bluelime_items = $bluelimeItemModel->where('id >=',1350)->findAll();
        $peachtree_items = $fromPeachtreeModel->where('quantity_on_hand !=',0)->where('bluelime_id',null)->findAll();

        $MATCH_FIRST_LETTER = $_GET['match_frist_letter'] ?? 3;

        $cnt = 0;
        $collection = [];
        foreach($peachtree_items as $index =>$peachtree_item){
            $peachtree_items[$index]['item_description'] = trim(substr($peachtree_item['item_description'],0,strlen($peachtree_item['item_description'])-3));
            $peachtree_item = $peachtree_items[$index];

            if(!isset($peachtree_item['chuncked_name'])){
                $peachtree_items[$index]['chuncked_name'] = $this->chunk_name($peachtree_item['item_description']);
                $peachtree_item = $peachtree_items[$index];
            }
            
            $peachtree_items[$index]['similarity'] = ['avarage' => 0, 'bluelime_data' => []];
            $found = false;
            foreach($bluelime_items as $b_index => $bluelime_item){
                if(strtolower(substr($peachtree_item['item_description'],0,$MATCH_FIRST_LETTER)) != strtolower(substr($bluelime_item['peachtree_discription'],0,$MATCH_FIRST_LETTER)))
                    continue;

                if(!isset($bluelime_item['chuncked_name'])){
                    $bluelime_items[$b_index]['chuncked_name'] = $this->chunk_name($bluelime_item['peachtree_discription']);
                    $bluelime_item = $bluelime_items[$b_index];
                }

                [$avarage,$word_percent, $number_percent, $smilarity_val] = $this->calculate_string_similarity($peachtree_item['chuncked_name'], $bluelime_item['chuncked_name']);
                if($avarage > $peachtree_items[$index]['similarity']['avarage']){
                    $peachtree_items[$index]['similarity'] = [
                            'bluelime_data' => $bluelime_item,
                            'avarage' => $avarage
                        ];
                        $peachtree_items[$index]['avarage'] = $avarage;
                        $peachtree_items[$index]['word_percent'] = $word_percent;
                        $peachtree_items[$index]['number_percent'] = $number_percent;
                        $peachtree_items[$index]['smilarity_val'] = $smilarity_val;
                }
                $found = true;
            }
            if($found)
                if($peachtree_items[$index]['smilarity_val'] >= 50)
                    $collection[] = $peachtree_items[$index];

            if(++$cnt >= 10){
                break;//break;
            }
        }

        $collection = BluelimeSystem::sort_array($collection, ['avarage'], true);

        $view_data = [
            'peachtree_items' => $collection,
            'MATCH_FIRST_LETTER' => $MATCH_FIRST_LETTER
        ];

        return view('PeachtreeIdMap/similarity_map', $view_data);
    }

    private function calculate_string_similarity($peachtree_data, $bluelime_data){
        $match = 0;
        foreach($peachtree_data['words'] as $p_word){
            foreach($bluelime_data['words'] as $b_word){
                if(strtolower($p_word) == strtolower($b_word)){
                    //match found
                    $match++;
                    break;
                }
            }
        }
        $word_percent = ($match / count($peachtree_data['words'])) * 100;

        $number_match = 0;
        foreach($peachtree_data['numbers'] as $p_number){
            foreach($bluelime_data['numbers'] as $b_number){
                if($p_number == $b_number){
                    //match found
                    $number_match++;
                }
            }
        }

        $number_percent = 0;
        if(!empty($peachtree_data['numbers']))
            $number_percent = ($number_match / count($peachtree_data['numbers'])) * 100;

        $smilarity_val = similar_text(strtolower($peachtree_data['name']), strtolower($bluelime_data['name']));

        $smilarity_val = ($smilarity_val / strlen($peachtree_data['name'])) * 100;

        $avarage = (($word_percent*.2) + ($number_percent*.3) + ($smilarity_val*.5));
        return [intval($avarage),$word_percent, $number_percent, $smilarity_val];
    }

    private function chunk_name($name): array{
        $name = str_ireplace(['/','-','\\',chr(255), chr(160),"\t",'(',')'], ' ', $name);
        $name = preg_replace('/[^[:print:]]/', ' ', $name);
        
        $name_words = explode(' ',$name);
        $name_temp = [];
        foreach ($name_words as $item) {
            if(!empty(trim($item)))
                $name_temp[] = $item;
        }
        $name_words = $name_temp;

        preg_match_all('/\\d+\\.?\\d*/', $name, $name_numbers);
        $name_numbers = array_unique($name_numbers[0]);
        
        return ["name"=>$name, "words" => $name_words, "numbers" => $name_numbers];
    }

    public function update_mapping(){
        $fromPeachtreeModel = model('PeachtreeIdMap/FromPeachtreeModel');

        $peachtree_id = $this->request->getPost('peachtree_id');
        $bluelime_id = $this->request->getPost('bluelime_id');
        $action = $this->request->getPost('action');

        //$existing = $fromPeachtreeModel->find($peachtree_id);
        
        $data = [
            'bluelime_id_full' => $bluelime_id
        ];

        if(strtolower($action) == 'cancel')
            $data = ['bluelime_id' => null];

        
        $updated = $fromPeachtreeModel->update($peachtree_id, $data);
        if($updated && strtolower($action) != 'cancel'){
            return $this->response->setJSON(['status' => 'success', 'value' => 'newly_updated']);
        }
        elseif($updated && strtolower($action) == 'cancel'){
            return $this->response->setJSON(['status' => 'success', 'value' => 'deleted']);
        }
        else{
            return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed in database.']);
        }
    }

    // public function map_id(){
    //     $flag = '#4';

    //     $fromPeachtreeModel = model('FromPeachtreeModel');
    //     $bluelimeItemModel = model('BluelimeItemModel');
        
        
    //     $bluelime_items = $bluelimeItemModel->where('id <=',1350)->findAll();
    //     $peachtree_items = $fromPeachtreeModel->where('quantity_on_hand !=',0)->where('bluelime_id',null)->findAll();

    //     // $three_char = [];
    //     // foreach($bluelime_items as $row){
    //     //     $prefix = strtolower(substr(str_replace(' ','',$row['peachtree_description']),0,1));
    //     //     if(!isset($three_char[$prefix]))
    //     //         $three_char[$prefix] = [];
    //     //     $three_char[$prefix][] = $row;
    //     // }

    //     $total_updates = 0;
    //     $exact_matches = 0;
    //     $chunk_match = 0;

    //     echo '<table border="1"><tr><th></th><th></th><th>Peachtree</th><th>Bluelime</th><th>Number %</th><th>String %</th></tr>';
    //     $sn = 1;
    //     foreach($peachtree_items as $index =>$peachtree_item){
    //         //$prefix = strtolower(substr(str_replace(' ','',$peachtree_item['item_description']),0,1));
    //         $peachtree_items[$index]['item_description'] = trim(substr($peachtree_item['item_description'],0,strlen($peachtree_item['item_description'])-3));
    //         $peachtree_item = $peachtree_items[$index];
    //         //$segment = $three_char[$prefix] ?? [];

    //         foreach($bluelime_items as $row){

    //             if(strtolower(str_replace(' ', '',$peachtree_item['item_description'])) == strtolower(str_replace(' ','',$row['peachtree_description']))){
    //                 echo '<tr><td>'.$sn++.'</td><td>'.$peachtree_item['peachtree_id'].'</td><td>'.$peachtree_item['item_description'].'</td><td>'.$row['peachtree_description']."</td><td></td><td>Exact</td></tr>";
                    
    //                 //$fromPeachtreeModel->update($peachtree_item['peachtree_id'], ['bluelime_id' => $row['peachtree_id'],'flag' => $flag." EXACT_MATCH"]);
    //                 $total_updates++;
    //                 $exact_matches++;
    //                 break;
    //             }
    //             else{
    //                 [$string_percent, $number_percent, $peachtree_item_numbers, $bluelime_item_numbers] = $this->chuck_and_map($peachtree_item, $row, $flag, $fromPeachtreeModel);
    //                 if($string_percent >= 50 && $number_percent >= 51)
    //                 {
    //                     echo '<tr><td>'.$sn++.'</td><td>'.$peachtree_item['peachtree_id'].'</td><td>'.$peachtree_item['item_description'].'</td><td>'.$row['peachtree_description']."</td><td>".intval($number_percent)."%</td><td>".intval($string_percent),"%</td><td>[";
    //                     foreach($peachtree_item_numbers as $p_number)
    //                         echo "$p_number, ";
    //                     echo "]<br>[";
    //                     foreach($bluelime_item_numbers as $b_number)
    //                         echo "$b_number, ";
                        
    //                     echo "]</td><td><button onclick=\"update('".$peachtree_item['peachtree_id']."','".$row['peachtree_id']."',this)\">Update</button></td></tr>";

    //                     //$fromPeachtreeModel->update($peachtree_item['peachtree_id'], ['bluelime_id' => $row['peachtree_id'],'flag' => $flag." CHUNK_MATCH"]);
    //                     $total_updates++;
    //                     $chunk_match++;
    //                     break;
    //                 }
    //             }
    //         }
    //     }
    //     echo '</table>';

    //     echo "Exact matches: " . $exact_matches . "<br>";
    //     echo "Chunk matches: " . $chunk_match . "<br>";
    //     echo "Total updates: " . $total_updates . "<br>";

    //     echo view('update');
    //     //BluelimeSystem::print_associated_array($three_char);

    // }

    public function map_by_similarity(array $peachtree_item, array $bluelime_item, $flag, $fromPeachtreeModel): bool{
        $percent = 0;
        $val = similar_text(strtolower($peachtree_item['item_description']), strtolower($bluelime_item['peachtree_discription']), $percent);

        if($percent > 80)
            echo '<tr><td>'.$peachtree_item['item_description'].'</td><td>'.$bluelime_item['peachtree_discription']."</td><td>$val</td><td>$percent</td></tr>";

        return false;
    }


    public function chuck_and_map($peachtree_item, $bluelime_item, $flag, $fromPeachtreeModel): array{
        $peachtree_item['item_description'] = str_ireplace(['/','-','\\', chr(160), chr(255),"\t"], ' ', $peachtree_item['item_description']);
        $bluelime_item['peachtree_discription'] = str_ireplace(['/','-','\\',chr(255), chr(160),"\t"], ' ', $bluelime_item['peachtree_discription']);
        $peachtree_item['item_description'] = preg_replace('/[^[:print:]]/', ' ', $peachtree_item['item_description']);
        $bluelime_item['peachtree_discription'] = preg_replace('/[^[:print:]]/', ' ', $bluelime_item['peachtree_discription']);
        $peachtree_item_words = explode(' ',$peachtree_item['item_description']);
        $bluelime_item_words = explode(' ', $bluelime_item['peachtree_discription']);


        preg_match_all('/\\d+\\.?\\d*/', $peachtree_item['item_description'], $peachtree_item_numbers);
        preg_match_all('/\\d+\\.?\\d*/', $bluelime_item['peachtree_discription'], $bluelime_item_numbers);
        $peachtree_item_numbers = $peachtree_item_numbers[0];
        $bluelime_item_numbers = $bluelime_item_numbers[0];

        $peachtree_item_words2 = [];
        foreach($peachtree_item_words as $row){
            $row = trim($row);
            if(empty($row))
                continue;
            // if(intval($row)>0)
            //     $peachtree_item_words2[] = ['word' => $row, 'val'=>intval($row), 'is_number' => true,'matched' => false];
            // else
                $peachtree_item_words2[] = ['word' => $row, 'is_number' => false,'matched' => false];
        }

        $bluelime_item_words2 = [];
        foreach($bluelime_item_words as $row){
            $row = trim($row);
            if(empty($row))
                continue;

            // if(intval($row)>0)
            //     $bluelime_item_words2[] = ['word' => $row, 'val'=>intval($row), 'is_number' => true,'matched' => false];
            // else
                $bluelime_item_words2[] = ['word' => $row, 'is_number' => false,'matched' => false];
        }

        foreach($peachtree_item_words2 as $index => $p_word){
            foreach($bluelime_item_words2 as $b_word){
                // if($p_word['is_number'] && $b_word['is_number']){
                //     if($p_word['val'] == $b_word['val']){
                //         //match found
                //         $peachtree_item_words2[$index]['matched'] = true;
                //         break;
                //     }
                // }
                // else{
                    if(strtolower($p_word['word']) == strtolower($b_word['word'])){
                        //match found
                        $peachtree_item_words2[$index]['matched'] = true;
                        break;
                    }
                //}
            }
        }

        $match = 0;
        foreach($peachtree_item_words2 as $p_word){
            if($p_word['matched'])
                $match++;
        }

        $peachtree_item_numbers = array_unique($peachtree_item_numbers);
        $bluelime_item_numbers = array_unique($bluelime_item_numbers);
        $number_match = 0;
        foreach($peachtree_item_numbers as $p_number){
            foreach($bluelime_item_numbers as $b_number){
                if($p_number == $b_number){
                    //match found
                    $number_match++;
                }
            }
        }


        $string_percent = ($match / count($peachtree_item_words2)) * 100;
        $number_percent = 0;

        if(empty($peachtree_item_numbers) && empty($bluelime_item_numbers))
            $number_percent = 99;
        elseif(empty($peachtree_item_numbers) && !empty($bluelime_item_numbers))
            $number_percent = 95;
        else
            $number_percent = ($number_match / count($peachtree_item_numbers)) * 100;



        return [$string_percent, $number_percent, $peachtree_item_numbers, $bluelime_item_numbers];
    }

    
}
