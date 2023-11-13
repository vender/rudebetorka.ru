<?php
session_start();
require_once './config.php';
$token = bin2hex(openssl_random_pseudo_bytes(16));

// If User has already logged in, redirect to dashboard page.
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === TRUE) {
	header('Location:index.php');
}

// If user has previously selected "remember me option": 
if (isset($_COOKIE['series_id']) && isset($_COOKIE['remember_token'])) {
	// Get user credentials from cookies.
	$series_id = filter_var($_COOKIE['series_id']);
	$remember_token = filter_var($_COOKIE['remember_token']);
	$db = getDbInstance();
	// Get user By series ID: 
	$db->where('series_id', $series_id);
	$row = $db->getOne('admin_accounts');

	if ($db->count >= 1) {
		// User found. verify remember token
		if (password_verify($remember_token, $row['remember_token'])) {
			// Verify if expiry time is modified. 
			$expires = strtotime($row['expires']);

			if (strtotime(date('Y-m-d H:i:s')) > $expires) {
				// Remember Cookie has expired. 
				clearAuthCookie();
				header('Location:login.php');
				exit;
			}

			$_SESSION['user_logged_in'] = TRUE;
			$_SESSION['admin_type'] = $row['admin_type'];
			$_SESSION['user_name'] = $row['user_name'];
			header('Location:index.php');
			exit;
		} else {
			clearAuthCookie();
			header('Location:login.php');
			exit;
		}
	} else {
		clearAuthCookie();
		header('Location:login.php');
		exit;
	}
}

include BASE_PATH . '/includes/header.php';
?>

<main class="form-signin">
	<form class="form loginform" method="POST" action="authenticate.php">
		<h1 class="h3 mb-3 fw-normal">Авторизация</h1>

		<div class="form-floating">
			<input type="text" name="username" class="form-control" id="floatingInput" placeholder="name@example.com">
			<label for="floatingInput">Логин</label>
		</div>
		<div class="form-floating">
			<input type="password" name="passwd" class="form-control" id="floatingPassword" placeholder="Password">
			<label for="floatingPassword">Пароль</label>
		</div>

		<div class="checkbox mb-3">
			<label>
				<input name="remember" type="checkbox" value="1"> Запомнить
			</label>
		</div>
		<?php if (isset($_SESSION['login_failure'])) : ?>
			<div class="alert alert-danger alert-dismissable fade in">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<?php
				// echo $_SESSION['login_failure'];
				unset($_SESSION['login_failure']);
				?>
			</div>
		<?php endif; ?>
		<button class="w-100 btn btn-lg btn-primary" type="submit">Войти</button>
	</form>
</main>

<style>
	html, body {
    	height: 100%;
	}
	body {
		display: flex;
		align-items: center;
		padding-top: 40px;
		padding-bottom: 40px;
		background-color: #f5f5f5;
		text-align: center!important;
	}

	.form-signin {
		width: 100%;
		max-width: 330px;
		padding: 15px;
		margin: auto;
	}

	.form-signin .checkbox {
		font-weight: 400;
	}

	.form-signin .form-floating:focus-within {
		z-index: 2;
	}

	.form-signin input[type="email"] {
		margin-bottom: -1px;
		border-bottom-right-radius: 0;
		border-bottom-left-radius: 0;
	}

	.form-signin input[type="password"] {
		margin-bottom: 10px;
		border-top-left-radius: 0;
		border-top-right-radius: 0;
	}
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>