<div style="color:red; font-weight:bold; font-size: 20px;"><?php echo $this->message;?></div>
<h1>Summary</h1>
<p>The User Dao Instance name : <?php echo $this->instanceName?></p>

<p>The choosen user's full class name is <?php echo htmlentities($this->namespace) ?></p>
<p>The repository of the user entity is the class name above</p>
<p>The name of the entityManager's instance : <?php echo $this->entityManagerName;?></p>
<form action="createMyInstance">

	<input type="hidden" name="selfedit" value="<?php echo $this->selfedit?>">
	<input type="hidden" name="choosen_namespace" value="<?php echo htmlentities($this->namespace)?>">
	<button>Confirm the creation of the UserDao's instance  </button>
</form>

<form action="skip">

	<input type="hidden" name="selfedit" value="<?php echo $this->selfedit?>">
	<input type="hidden" name="choosen_namespace" value="<?php echo htmlentities($this->namespace)?>">
	<button>Skip the userDao creation </button>
</form>