<?php
if(isset($_SESSION['success']))
{

echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    		<strong>Успешно! </strong>'. $_SESSION['success'].'
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
  	  </div>';
  unset($_SESSION['success']);
}

if(isset($_SESSION['failure']))
{
echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    		<strong>Oops! </strong>'. $_SESSION['failure'].'
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
  	  </div>';
  unset($_SESSION['failure']);
}

if(isset($_SESSION['info']))
{
echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
    		'. $_SESSION['info'].'
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
  	  </div>';
  unset($_SESSION['info']);
}

 ?>