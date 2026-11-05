<div>
    <div class="container">
        <!-- Trigger the modal with a button -->
        <!-- New General modal -->
        <div class="modal fade" id="addNewGeneral" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span id="addNewGeneral_title">Title</span></h4>
                    </div>
                    <div class="modal-body">
                        <label class="control-label" for="addNewGeneral_name">Title</label>
                        <input id="addNewGeneral_name" type="text" class="form-control" value="">
                        <br>
                        <div style="display: none;">
                            <label class="control-label" for="addNewGeneral_description">Description</label>
                            <textarea id="addNewGeneral_description" type="text" class="form-control"></textarea>
                        </div>
                        <br>
                        <div id="addNewGeneral_Flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="addNewGeneral_BtnAdd" type="button" class="btn btn-success" onclick="">Add</button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var auto_compelete = <?= BluelimeSystem::convert_array_to_string(($auto_complete_data ?? [])) ?>;
    function show_add_new_modal(title, name){
        $("#addNewGeneral_title").html(title);
        $("#addNewGeneral_name").val('');
        $("#addNewGeneral_description").val('');
        dental_hide_flash("addNewGeneral_Flash");
        $('#addNewGeneral_BtnAdd').off().on('click', function (){add_new_general(name)});
        $('#addNewGeneral').modal('show');
    }
    function show_add_new_nurse_modal(title, name){
        $("#addNewGeneral_title").html(title);
        $("#addNewGeneral_name").val('');
        $("#addNewGeneral_description").val('');
        dental_hide_flash("addNewGeneral_Flash");
        $('#addNewGeneral_BtnAdd').off().on('click', function (){add_new_nurse_general(name)});
        $('#addNewGeneral').modal('show');
    }

    function add_new_general(name){
        let value = $("#addNewGeneral_name").val();
        //let description = $("#addNewGeneral_description").val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/add_auto_complete_data",
            data: {"name":name, "value":value},
            success: function(result, textStatus, jqXHR){
                try{
                    if(result == '')
                        throw 'Error';
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        if(auto_compelete[name] != undefined)
                            auto_compelete[name].push(r.value);
                        else
                            auto_compelete[name] = [value];

                        $("#addNewGeneral_name").val('');
                        $("#addNewGeneral_description").val('');
                        close_modal('addNewGeneral');  
                    }
                    else{
                        error_html = "Status: " + r.status + "<br>" + r.message;
                        dental_show_flash('addNewGeneral_Flash', error_html, false);
                    }
                }
                catch (e){
                    error_html = "Invalid retun result.<br><u>Result:</u><br>"+result;
                    dental_show_flash('addNewGeneral_Flash', error_html, false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Cannot add to database. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('addNewGeneral_Flash', error_html, false);
            }
        });
    }
    
    function add_new_nurse_general(name){
        let value = $("#addNewGeneral_name").val();
        //let description = $("#addNewGeneral_description").val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/add_auto_complete_data",
            data: {"name":name, "value":value},
            success: function(result, textStatus, jqXHR){
                try{
                    if(result == '')
                        throw 'Error';
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        if(auto_compelete[name] != undefined)
                            auto_compelete[name].push(r.value);
                        else
                            auto_compelete[name] = [value];

                        $("#addNewGeneral_name").val('');
                        $("#addNewGeneral_description").val('');
                        close_modal('addNewGeneral'); 
                        location.reload(); 
                    }
                    else{
                        error_html = "Status: " + r.status + "<br>" + r.message;
                        dental_show_flash('addNewGeneral_Flash', error_html, false);
                    }
                }
                catch (e){
                    error_html = "Invalid retun result.<br><u>Result:</u><br>"+result;
                    dental_show_flash('addNewGeneral_Flash', error_html, false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Cannot add to database. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('addNewGeneral_Flash', error_html, false);
            }
        });
    }

</script>