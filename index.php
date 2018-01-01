<?php 
ob_start();
/* Access key */
define('ACCESS_KEY', '12345');

/* Connection to database */
define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', '');

/* Debug mode */
$debug = false;

$notif['creation-success'] = false;
$notif['creation-fail'] = false;
$notif['wrong-key'] = false;
$notif['wrong-username'] = false;


$stone_list = json_decode(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'stone_list.json'));
$bonus_list = json_decode(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'bonus_list.json'));

try {
	$conn = @new PDO('mysql:host='.DBHOST, DBUSER, DBPASS);
} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}

if (isset($_POST['create'])) {
	if ($_POST['access-pass'] == ACCESS_KEY) {
		foreach ($_POST as $key=>$value) {
			$key = filter_var($value, FILTER_SANITIZE_STRING);
		}

		if ($debug) {
			var_dump($_POST);
		}

		$chkAcc = $conn->prepare("SELECT id FROM account.account WHERE login = ?");
		$res = $chkAcc->execute([$_POST['username']]);
		if ($chkAcc->rowCount() >= 1) {
			$qp = [
				'owner_id' => $chkAcc->fetchObject()->id,
				'window' => $_POST['window'],
				'position' => $_POST['position'],
				'count' => 1,
				'vnum' => $_POST['vnum'],
				'socket0' => $_POST['stone1'],
				'socket1' => $_POST['stone2'],
				'socket2' => $_POST['stone3'],
				'attrtype0' => $_POST['bonus1'],
				'attrtype1' => $_POST['bonus2'],
				'attrtype2' => $_POST['bonus3'],
				'attrtype3' => $_POST['bonus4'],
				'attrtype4' => $_POST['bonus5'],
				'attrtype5' => $_POST['bonus6'],
				'attrtype6' => $_POST['bonus7'],
				'attrvalue0' => $_POST['bonus1-amm'],
				'attrvalue1' => $_POST['bonus2-amm'],
				'attrvalue2' => $_POST['bonus3-amm'],
				'attrvalue3' => $_POST['bonus4-amm'],
				'attrvalue4' => $_POST['bonus5-amm'],
				'attrvalue5' => $_POST['bonus6-amm'],
				'attrvalue6' => $_POST['bonus7-amm']
			];


			$createQuery = $conn->prepare("INSERT INTO `player`.`item` (`id`, `owner_id`, `window`, `pos`, `count`, `vnum`, `socket0`, `socket1`, `socket2`, `socket3`, `socket4`, `socket5`, `attrtype0`, `attrvalue0`, `attrtype1`, `attrvalue1`, `attrtype2`, `attrvalue2`, `attrtype3`, `attrvalue3`, `attrtype4`, `attrvalue4`, `attrtype5`, `attrvalue5`, `attrtype6`, `attrvalue6`) VALUES (NULL, :owner_id, :window, :position, :count, :vnum, :socket0, :socket1, :socket2, 0, 0, 0, :attrtype0, :attrvalue0, :attrtype1, :attrvalue1, :attrtype2, :attrvalue2, :attrtype3, :attrvalue3, :attrtype4, :attrvalue4, :attrtype5, :attrvalue5, :attrtype6, :attrvalue6);");
			$res = $createQuery->execute($qp);

			

			if ($res) {
				$notif['creation-success'] = true;
			} else {
				$notif['creation-fail'] = true;
				if ($debug) {
					var_dump($createQuery->errorInfo());
				}
			}
			if ($debug) {
				var_dump($qp);
			}
		} else {
			$notif['wrong-username'] = true;
		}
	} else {
		$notif['wrong-key'] = true;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Mod Items</title>
	<link rel="stylesheet" href="css/bootstrap.css">
	<style>
		body{
			background-color: #111122;
		}
		footer{
			margin-bottom: 20px;
		}
		.container{
			max-width: 768px !important;
			margin-top: 40px;
		}
	</style>
</head>
<body>
<div class="well container">
	<form method="POST" class="form-horizontal">
		<h4 class="text-center">Modare iteme</h4>
		<hr>
		<?php if ($notif['creation-success']): ?>
			<div class="alert alert-success">
				<strong>Succes!</strong> Itemul a fost creat!
			</div>
		<?php endif ?>
		<?php if ($notif['creation-fail']): ?>
			<div class="alert alert-danger">
				<strong>Eroare!</strong> Itemul nu a putut fi creat!
			</div>
		<?php endif ?>
		<?php if ($notif['wrong-key']): ?>
			<div class="alert alert-danger">
				<strong>Eroare!</strong> Cheia de acces nu este corecta!
			</div>
		<?php endif ?>
		<?php if ($notif['wrong-username']): ?>
			<div class="alert alert-danger">
				<strong>Eroare!</strong> Cheia de acces nu este corecta!
			</div>
		<?php endif ?>
		<div class="form-group<?= $notif['wrong-username'] ? ' has-error' : '' ?>">
			<label for="username" class="control-label col-sm-4">Username</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="username" id="username">
			</div>
		</div>
		<div class="form-group<?= $notif['wrong-key'] ? ' has-error' : '' ?>">
			<label for="access-pass" class="control-label col-sm-4">Cheie acces</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="access-pass" id="access-pass">
			</div>
		</div>
		<div class="form-group">
			<label for="window" class="control-label col-sm-4">Depozit</label>
			<div class="col-sm-5">
				<select name="window" id="window" class="form-control">
					<option value="MALL">ItemShop</option>
					<option value="SAFEBOX">Depozit normal</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="position" class="control-label col-sm-4">Pozitie</label>
			<div class="col-sm-5">
				<input type="number" class="form-control" name="position" id="position">
			</div>
		</div>
		<div class="form-group">
			<label for="itemcode" class="control-label col-sm-4">Cod item</label>
			<div class="col-sm-5">
				<input type="number" class="form-control" name="vnum" id="itemcode">
			</div>
		</div>
		<hr>
		<div class="form-group">
			<label for="stone1" class="control-label col-sm-4">Piatra 1</label>
			<div class="col-sm-5">
				<select name="stone1" id="stone1" class="form-control">
					<?php foreach ($stone_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="stone2" class="control-label col-sm-4">Piatra 2</label>
			<div class="col-sm-5">
				<select name="stone2" id="stone2" class="form-control">
					<?php foreach ($stone_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="stone3" class="control-label col-sm-4">Piatra 3</label>
			<div class="col-sm-5">
				<select name="stone3" id="stone3" class="form-control">
					<?php foreach ($stone_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<label for="bonus1" class="control-label col-sm-3">Bonus 1</label>
			<div class="col-sm-4">
				<select name="bonus1" id="bonus1" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus1-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus2" class="control-label col-sm-3">Bonus 2</label>
			<div class="col-sm-4">
				<select name="bonus2" id="bonus2" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus2-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus3" class="control-label col-sm-3">Bonus 3</label>
			<div class="col-sm-4">
				<select name="bonus3" id="bonus3" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus3-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus4" class="control-label col-sm-3">Bonus 4</label>
			<div class="col-sm-4">
				<select name="bonus4" id="bonus4" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus4-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus5" class="control-label col-sm-3">Bonus 5</label>
			<div class="col-sm-4">
				<select name="bonus5" id="bonus5" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus5-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus6" class="control-label col-sm-3">Bonus 6</label>
			<div class="col-sm-4">
				<select name="bonus6" id="bonus6" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus6-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<div class="form-group">
			<label for="bonus7" class="control-label col-sm-3">Bonus 7</label>
			<div class="col-sm-4">
				<select name="bonus7" id="bonus7" class="form-control">
					<?php foreach ($bonus_list as $key => $value): ?>
						<option value="<?= $key ?>"><?= $value ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<input type="number" name="bonus7-amm" class="form-control" placeholder="max 32676">
			</div>
		</div>
		<hr>
		<center>
			<button type="submit" name="create" class="btn btn-primary">Creaza</button>
		</center>
	</form>
</div>
<footer class="text-center">
	<?= date('Y') ?> <br>
	Coded by <a href="https://meclaud.github.io/">MeClaud</a> under <a href="https://opensource.org/licenses/MIT">MIT license</a>
</footer>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
