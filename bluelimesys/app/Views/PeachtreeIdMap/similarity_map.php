
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>

<style>
    .description{
        border: solid 1px #ccc;
        border-radius: 5px;
        padding: 5px;
        margin: 2px;
    }
</style>
<div class="p-5">
    <div class="col-md-9">
        <h3>Peachtree to Bluelime ID Mapper</h3>
        <form method="GET">
            <div class="row">
                <div class="col-lg-3">
                    <label for="show_similarity">Show similarity values</label> <input type="checkbox" id="show_similarity" value="1" name="show_similarity" <?=  (($_GET['show_similarity']??0)==1)?'checked':''?>>
                </div>
                <div class="col-lg-3">
                    
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <label for="match_frist_letter">Match frist letter</label>
                </div>
                <div class="col-lg-3">
                    <input type="number" id="match_frist_letter" name="match_frist_letter" value="<?= $MATCH_FIRST_LETTER ?>" class="form-control">
                </div>
                <div class="col-lg-3">
                    <input type="submit" value="Filter" class="form-control">
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead>
                <tr style="position: fixed; background-color: #ccc; top: 0px;">
                    <th width="80">Sn</th><th width="367">Peachtree</th><th  width="185">Location</th>
                    <?=  (($_GET['show_similarity']??0)==1)?'<th  width="45">Sim%</th><th width="65">W%</th><th width="70">N$</th><th width="70">V%</th>':'' ?>
                    <th width="150">Action</th>
                </tr>
                <tr>
                    <th width="80">Sn</th><th width="367">Peachtree</th><th  width="185">Location</th>
                    <?=  (($_GET['show_similarity']??0)==1)?'<th  width="45">Sim%</th><th width="65">W%</th><th width="70">N$</th><th width="70">V%</th>':'' ?>
                    <th width="150">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sn = 1;
                    foreach($peachtree_items as $row){
                        echo '<tr>';
                        echo '<td width="65">'.$sn++.'</td>';
                        echo '<td width="300">
                                <div class="description">'.$row['item_description'].'</div>
                                <div class="description">'.$row['similarity']['bluelime_data']['peachtree_discription'].'</div>
                              </td>';
                        echo '<td width="150">
                                <span class="badge bg-info">'.(substr($row['location'],-1,1)=='1'?'Wollo':'Lebu').'</span> <span class="badge bg-danger">'.(substr($row['location'],0,2)=='ST'?'Store':'Disp').'</span>
                              </td>';

                        if(($_GET['show_similarity']??0)==1){
                            echo '<td width="45">'.$row['avarage'].'</td>';
                            echo '<td width="57">'.intval($row['word_percent']).'</td>';
                            echo '<td width="57">'.intval($row['number_percent']).'</td>';
                            echo '<td width="57">'.intval($row['smilarity_val']).'</td>';
                        }
                        echo '<td width="120"><button class="btn btn-success" onclick="accept(\''.addslashes($row['peachtree_id']).'\', \''.addslashes($row['similarity']['bluelime_data']['peachtree_code']).'\', this)">Accept</button></td>';
                        echo '</tr>';
                    } 
                ?>
            </tbody>
        </table>

    </div>
</div>
<script>
    function accept(peachtree_id, bluelime_id, btn){
        let lastAction = btn.innerText;
        $.ajax({
            url: '/peachtreeidmap/update_mapping',
            type: 'POST',
            data: {
                peachtree_id: peachtree_id,
                bluelime_id: bluelime_id,
                action: btn.innerText
            },
            dataType: 'json',
            beforeSend: function() {
                btn.innerText = 'Updating...';
                btn.disabled = true;
            },
            success: function(response) {
                //alert('Update successful!');
                //btn.disabled = true;
                if(response.status === 'success'){
                    if(response.value == 'newly_updated'){
                        btn.innerText = 'Cancel';
                        $(btn).toggleClass('btn-success btn-warning');
                    }
                    else{
                        btn.innerText = 'Accept';
                        $(btn).toggleClass('btn-success btn-warning');
                    }
                }
                else{
                    alert('Update failed: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Update failed: ' + error);
                btn.innerText = lastAction;
            },
            complete: function() {
                btn.disabled = false;
            }
        });
    }
</script>
