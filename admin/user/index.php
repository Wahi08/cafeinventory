<?php 
$user = $conn->query("SELECT * FROM users where id ='".$_settings->userdata('id')."'");
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-teal">
	<div class="card-body">
		<div class="container-fluid">

			<div id="errorMsg"></div>
			<div id="msg"></div>

			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo $_settings->userdata('id') ?>">
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="name">Full Name</label>
							<input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo isset($meta['fullname']) ? $meta['fullname']: '' ?>">
						</div>
						
						<div class="form-group">
							<label for="email">E-Mail</label>
							<input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>">
						</div>
						
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" autocomplete="off">
						</div>
						
						<div class="form-group">
							<label for="contact">Contact</label>
							<input type="text" name="contact" id="contact" class="form-control" value="<?php echo isset($meta['contact']) ? $meta['contact']: '' ?>" autocomplete="off">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
							<small><i>Leave this blank if you dont want to change the password.</i></small>
						</div>
						<div class="form-group">
							<label for="" class="control-label">Profile Picture</label>
							<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary" form="manage-user">Update</button>
				</div>
			</div>
		</div>
</div>

<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
			var file = input.files[0];
			var fileType = file.type;

			// Check if the file type is valid
			var validFileTypes = ['image/jpeg', 'image/png'];
			if (!validFileTypes.includes(fileType)) {
				$('#errorMsg').html('<div class="alert alert-danger">Invalid file type. Only JPEG and PNG files are allowed.</div>');
				return; // Exit the function if the file type is invalid
			}

	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			$('#cimg').attr('src', "<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>");
		}
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			// Inside the Ajax success callback
			success: function(resp) {
			if (resp == 1) {
				location.reload();
			} else {
				if (resp.error) {
				$('#errorMsg').html('<div class="alert alert-danger">' +resp.error + '</div>').show();
				$('#msg').empty().hide();
				} else {
				$('#msg').html('<div class="alert alert-danger">Error occurred while updating the image.</div>').show();
				$('#errorMsg').empty().hide();
				}
				end_loader();
			}
			}
		})
	})

</script>