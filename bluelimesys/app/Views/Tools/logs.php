<?php
    
    function print_tree($file_name_lists){
        foreach($file_name_lists as  $file){
            if($file['type'] == 'file')
                echo '<li><a class="file_name_link" onclick="load_log(\''.addslashes($file['path']).'\', \''.$file['name'].'\')">'.$file['name'].'</a></li>';
            else{
                echo '<li><a class="file_name_link" onclick="show_hide(this)">'.$file['name'].'</a>';
                echo '<ul class="folder">';
                print_tree($file['children']);
                echo '</li></ul>';
            }
        }
    }
?>
<style>
    .file_name_link{
        display: inline-block;
        margin: 2px;
        cursor: pointer;
        border: solid 1px #ddd;
        border-radius: 5px;
        padding: 5px;
    }

    .folder{
        display: none;
    }
</style>
<table border="1">
    <tr>
        <td>
            <ul>
            <?= print_tree($file_name_list) ?>
            </ul>
        </td>
        <td style="vertical-align: top;">
            <div style="position: fixed; padding:20px;">
                <div><b id="lbl_file_name"></b></div>
                <pre style="width:800px; height: 500px; overflow: scroll; border:solid 1px" id="log">
                </pre>
            </div>
        </td>
    </tr>
</table>

<script>
    var xhttp = new XMLHttpRequest();
    function load_log(path, file_name){

        xhttp.onload = function() {
            document.getElementById("log").innerHTML = "";
            document.getElementById("log").innerHTML = this.responseText;
            document.getElementById("lbl_file_name").innerHTML = path + '/<br>' +file_name;

        }
        xhttp.open("POST", "/tools/read_log_content");
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("file_name="+path+'/'+file_name);
        
    }

    var o = null;
    function show_hide(folder_name){
        o = folder_name;
        obj = folder_name.parentElement;
        let disp = obj.children[1].style.display;
        if(disp == 'block')
            obj.children[1].style.display = 'none';
        else
            obj.children[1].style.display = 'block';
    }
</script>