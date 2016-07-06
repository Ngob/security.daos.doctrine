<?php /* @var $this \Security\Daos\Doctrine\UserEntityInstallerController */ ?>
<h1>Setting up your instance</h1>

<p>The security.doctrine.userdao package can create automatically a class for your User entity. It also create an instance of userdao
Select the correct full class name (with the namespace) for the user's class.</p>
<p>To use this installer you must select a full class name (with the namespace) for the user's class</p>

<form action="selectFilename" id="namespace-form-install">
    <input type="hidden" name="selfedit" value="<?php echo $this->selfedit ?>" />
    <?php 
    	$fullnamespaces = $this->_getCalculedFullyQualifiedUserEntityNameSpace();
    	$cssId = 0;
    	$hasBeenChecked = false;
       	foreach ($fullnamespaces as $fullnamespace):
    ?>
    	<input style="float:left" <?php echo $hasBeenChecked == false ? 'checked="checked"': "" ?> type="radio" id="radio-namespace-<?php echo (int) $cssId; ?>" name="choosen_namespace" value="<?php echo htmlentities($fullnamespace)?>">
    	<label style="float:left" for="radio-namespace-<?php echo (int) $cssId; ?>">
    		<?php echo htmlentities($fullnamespace)?>
    	</label>
    <?php 
    	$cssId++;
    	$hasBeenChecked = true;
    	endforeach;
    ?>
    <input style="clear:both;float:left" <?php echo $hasBeenChecked == false ? 'checked="checked"': "" ?>  type="radio" id="radio-namespace-<?php echo (int) $cssId; ?>" name="choosen_namespace" value="">
    <label style="float:left" for="radio-namespace-<?php echo (int) $cssId; ?>">
    	Other:
    </label>
    <input style="float:left" type="text" id="other_namespace" name="other_namespace" value="">
    <button style="clear:both; display:block; float:left; margin-right: 10px;" data-submit="" class="btn btn-primary">Create The UserEntity</button>
</form>
<form style="float:left;display:block" action="skip">
    <input type="hidden" name="selfedit" value="<?php echo $this->selfedit ?>" />
    <button class="btn">Skip this installer</button>
</form>
<script>
    document.getElementById("namespace-form-install").addEventListener("submit", function(e) {
        "use strict";
        e = e || window.event;
        e.preventDefault();
        //e.stopImmediatePropagation();
    	var target = e.target || null;
    	if (!target || typeof(target) === "undefined")
            throw "no target";
        var radioOthers = target.querySelector('#radio-namespace-<?php echo (int) $cssId; ?>:checked');
        if (!radioOthers || typeof(radioOthers) === "undefined") {
        	target.submit();
            return true;
        }
        var textOtherNs = document.getElementById('other_namespace');
        if (!textOtherNs || typeof(textOtherNs) === "undefined")
            throw "no input text found";
        if (!textOtherNs || textOtherNs.value.trim().length === 0) {
            alert("Please You must fill a classname");
            return false;
        }
        radioOthers.value = textOtherNs.value;
       	target.submit();
       	return true;
    });
</script>