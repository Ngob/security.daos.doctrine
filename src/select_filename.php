<div style="color:red; font-weight:bold; font-size: 20px;"><?php echo $this->message;?></div>
<h1>If you want to use the default user class . It will create a default user class at the selected the file path (must be absolute).</h1>
<form action="checkFile" id="filename-form-install">
    <input type="hidden" name="selfedit" value="<?php echo $this->selfedit ?>" />
    <input type="hidden" name="choosen_namespace" value="<?php echo $this->namespace ?>" />
    <h3>Automatically detected filepath</h3>
    <?php 
    	$count = 1;
    	$hasBeenChecked = false;
    	foreach($this->fileList as $file):
    ?>
    <input <?php echo $file["exist"] ? "disabled=disabled" : ""?> style="float:left"  type="radio" id="radio-filename-<?php echo $count ?>" name="choosen_filename" value="<?php echo htmlentities($file["path"])?>" <?php echo ($hasBeenChecked == false && !$file["exist"]) ? 'checked="checked"': "" ?>>
    	<label style="float:left" for="radio-filename-<?php echo $count ?>">
    		<?php echo htmlentities($file["path"])?>
    	</label>
    	<?php if ($file["exist"]) : ?> 
    	<span style=" color:red; font-weight:bold">
    		&nbsp;&nbsp;This file already Exist, please enter another filename or skip the user entity creation
    	</span>
    	<?php endif;?>
    	<br/>
    	<div style="clear:both"></div>
    <?php 
    	$count++;
    	if (!$file["exist"]) {
    		$hasBeenChecked = true;
    	}
    endforeach;
    ?>
    <h3>... Or choose your filename :</h3>
    <div style="clear:both"></div> 
     <input style="float:left"  type="radio" id="radio-filename-<?php echo $count ?>" name="choosen_filename" value="" <?php echo $hasBeenChecked == false ? 'checked="checked"': "" ?>>
    	<label style="float:left" for="radio-filename-<?php echo $count ?>">
    		<input type="text" id="other_filename" name="other_filename" value="">
    	</label>
    <button style="clear:both; display:block; float:left; margin-right: 10px;" data-submit="">Create The UserEntity</button>
</form>
<div style="clear:both"></div>
<h1>If you do not want to create the default user class click below</h1>
<form action="createMyInstanceView">
    <input type="hidden" name="selfedit" value="<?php echo $this->selfedit ?>" />
    <input type="hidden" name="choosen_namespace" value="<?php echo $this->namespace ?>" />
    <button style="clear:both; display:block; float:left; margin-right: 10px;" data-submit="">Skip</button>
</form>

<script>
    document.getElementById("filename-form-install").addEventListener("submit", function(e) {
        "use strict";
        e = e || window.event;
        e.preventDefault();
        //e.stopImmediatePropagation();
    	var target = e.target || null;
    	if (!target || typeof(target) === "undefined")
            throw "no target";
        var radioOthers = target.querySelector('#radio-filename-<?php echo (int) $count; ?>:checked');
        if (!radioOthers || typeof(radioOthers) === "undefined") {
        	target.submit();
            return true;
        }
        var textOtherNs = document.getElementById('other_filename');
        if (!textOtherNs || typeof(textOtherNs) === "undefined")
            throw "no input text found";
        if (!textOtherNs || textOtherNs.value.trim().length === 0) {
            alert("Please You must fill a name space");
            return false;
        }
        radioOthers.value = textOtherNs.value;
       	target.submit();
       	return true;
    });
</script>