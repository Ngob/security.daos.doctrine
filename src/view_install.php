<h1>Setting up your instance</h1>

<p>Our package can create automatically a <em>myInstance</em> instance for the class <em>myClass</em>.
So you want to create it?</p>

<form action="install" id="namespace-form-install">
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
    	Others: 
    </label>
    <input style="float:left" type="text" id="other_namespace" name="other_namespace" value="">
    <button style="clear:both; display:block; float:left; margin-right: 10px;" data-submit="">Create The UserEntity</button>
</form>
<form style="float:left;display:block" action="skip">
    <input type="hidden" name="selfedit" value="<?php echo $this->selfedit ?>" />
    <button>Do Not create the user entity</button>
</form>
<script>
    document.getElementById("namespace-form-install").addEventListener("submit", function(e) {
        console.log("foo");
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
            alert("Please You must fill a name space");
            return false;
        }
        radioOthers.value = textOtherNs.value;
       	target.submit();
       	return true;
    });
</script>