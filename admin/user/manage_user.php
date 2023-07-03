<?php
if (isset($_GET['id'])) {
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}' ");
    foreach ($user->fetch_array() as $k => $v) {
        $meta[$k] = $v;
    }
}
?>
<?php if ($_settings->chk_flashdata('success')) : ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<div class="card card-outline rounded-0 card-teal">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-user" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= isset($meta['id']) ? $meta['id'] : '' ?>">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo isset($meta['fullname']) ? $meta['fullname'] : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>">
                            <span id="email-validation" style="display: none; color:red;font-weight: bold;">*Invalid email format</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact</label>
                            <input type="text" name="contact" id="contact" class="form-control" value="<?php echo isset($meta['contact']) ? $meta['contact'] : '' ?>" required pattern="[0-9]{10,11}" title="Please enter a valid 10 or 11-digit contact number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password"><?= isset($meta['id']) ? "New" : "" ?> Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
                            <?php if (isset($meta['id'])) : ?>
                                <small><i>Leave this blank if you dont want to change the password.</i></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Profile Picture</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/jpeg,image/png" onchange="displayImg(this)">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <span>
                                    <div id="cimg-container">
                                        <img id="cimg" class="img-fluid" alt="Profile Picture">
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type" class="control-label">User Type</label>
                            <select name="type" id="type" class="form-control form-control-sm rounded-0" required>
                                <option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected' : '' ?>>Administrator</option>
                                <option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>Manager</option>
                                <option value="3" <?php echo isset($meta['type']) && $meta['type'] == 3 ? 'selected' : '' ?>>Staff</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row">
                <button class="btn btn-sm btn-primary rounded-0 mr-3" form="manage-user">Save</button>
            </div>
        </div>
    </div>
</div>
<style>
    img#cimg {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 50%;
    }
    #cimg-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
    }
</style>
<script>
    $(document).ready(function () {
        var emailInput = $('#email');
        var emailValidation = $('#email-validation');

        emailInput.on('input', function () {
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
                    var dimension = 150; // Dimension of the circular image

                    canvas.width = dimension;
                    canvas.height = dimension;

                    ctx.beginPath();
                    ctx.arc(dimension / 2, dimension / 2, dimension / 2, 0, 2 * Math.PI);
                    ctx.closePath();
                    ctx.clip();

                    var ratio = Math.max(dimension / img.width, dimension / img.height);
                    var width = img.width * ratio;
                    var height = img.height * ratio;
                    var x = (dimension - width) / 2;
                    var y = (dimension - height) / 2;

                    ctx.drawImage(img, x, y, width, height);

                    $('#cimg').attr('src', canvas.toDataURL('image/jpeg'));
                };
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#manage-user').submit(function (e) {
        e.preventDefault();
        start_loader()
        $.ajax({
            url: _base_url_ + 'classes/Users.php?f=save',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',

            success: function (resp) {
                if (resp == 1) {
                    location.href = './?page=user/list'
                } else {
                    $('#msg').html('<div class="alert alert-danger">Username already exists</div>')
                    end_loader()
                }
            }
        })
    });
</script>
