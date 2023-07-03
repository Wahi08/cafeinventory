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

            <form action="" id="manage-user" enctype="multipart/form-data">    
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
                            <span id="email-validation" style="display: none; color:red;font-weight: bold;">*Invalid email format</span>
                        </div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" autocomplete="off">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact">Contact</label>
                            <input type="text" name="contact" id="contact" class="form-control" value="<?php echo isset($meta['contact']) ? $meta['contact']: '' ?>" required pattern="[0-9]{10,11}" title="Please enter a valid 10 or 11-digit contact number" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
                            <small><i>Leave this blank if you don't want to change the password.</i></small>
                        </div>
                        <!-- ... -->
                        <div class="form-group">
                            <label for="" class="control-label">Profile Picture</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/jpeg,image/png" onchange="displayImg(this,$(this))">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $avatar = $_settings->userdata('avatar');
                            $imageData = base64_encode($avatar);
                            $imageType = $_settings->userdata('avatar_type');
                            $dataURI = 'data:' . $imageType . ';base64,' . $imageData;
                            ?>
                            <div class="form-group">
                                <span>
                                    <img id="cimg" src="<?php echo $dataURI; ?>" class="img-fluid rounded-circle" alt="Profile Picture">
                                </span>
                            </div>
                        </div>
                        <!-- ... -->
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row">
                <button class="btn btn-sm btn-primary rounded-0 mr-3" form="manage-user">Update</button>
            </div>
        </div>
    </div>
</div>

<style>
    img#cimg {
        height: 150px;
        width: 150px;
        object-fit: cover;
        border-radius: 50%;
    }
</style>
<script>
    $(document).ready(function() {
        var emailInput = $('#email');
        var emailValidation = $('#email-validation');

        emailInput.on('input', function() {
            validateEmail(emailInput.val());
        });

        function validateEmail(email) {
            // Email validation regex pattern
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailPattern.test(email)) {
                emailValidation.hide();
            } else {
                emailValidation.show();
            }
        }
    });

    function displayImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    var diameter = 150; // Diameter of the circular image

                    canvas.width = diameter;
                    canvas.height = diameter;

                    ctx.beginPath();
                    ctx.arc(diameter / 2, diameter / 2, diameter / 2, 0, 2 * Math.PI);
                    ctx.closePath();
                    ctx.clip();

                    var ratio = Math.max(diameter / img.width, diameter / img.height);
                    var width = img.width * ratio;
                    var height = img.height * ratio;
                    var x = (diameter - width) / 2;
                    var y = (diameter - height) / 2;

                    ctx.drawImage(img, x, y, width, height);

                    $('#cimg').attr('src', canvas.toDataURL('image/jpeg'));
                };
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#manage-user').submit(function(e) {
        e.preventDefault();
        start_loader();
        $.ajax({
            url: _base_url_+'classes/Users.php?f=save',
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
                        $('#errorMsg').html('<div class="alert alert-danger">' + resp.error + '</div>').show();
                        $('#msg').empty().hide();
                    } else {
                        $('#msg').html('<div class="alert alert-danger">Error occurred while updating the image.</div>').show();
                        $('#errorMsg').empty().hide();
                    }
                    end_loader();
                }
            }
        });
    });
</script>
