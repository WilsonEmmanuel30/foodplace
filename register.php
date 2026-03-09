<?php
// Adding pagetitle, header and database connection
$pagetitle = "Create an account";
require_once "assets/header.php";
require_once "assets/mailer.php";
require_once "assets/db_connect.php";

// Initializing variable
$picError = $fnerror = $lnerror = $pherror = $emerror = $perror = $cperror = $aerror = $msg = "";
$firstname = $lastname = $phone = $email = $password = $confirm_password = $allergies = "";

// Capture entries
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $picture = $_FILES['picture'];
    $firstname = htmlspecialchars($_POST['firstname'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // $firstname = $_POST['firstname'];
    $lastname = htmlspecialchars($_POST['lastname'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $confirm_password = htmlspecialchars($_POST['confirm_password'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $allergies = htmlspecialchars($_POST['allergies'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Validating firstname
    if (empty($firstname)) {
        $fnerror = "Firstname is required";
    }

    // Validating lastname
    if (empty($lastname)) {
        $lnerror = "Lastname is required";
    }

    // Validating phone
    if (empty($phone)) {
        $pherror = "Phone number is required";
    } else if (!preg_match('/^(0|234|\+234)(70|80|81|90|91)\d{8}$/', $phone)) {
        $pherror = "Invalid phone number format";
    }

    // Validating email
    if (empty($email)) {
        $emerror = "E-mail address is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emerror = "Invalid email address";
    }

    // Validating password
    if (empty($password)) {
        $perror = "Password is required";
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $perror = "Invalid password format";
    } else if ($password !== $confirm_password) {
        $perror = $cperror = "Password does not match";
    }

    // Validating confirm password
    if (empty($confirm_password)) {
        $cperror = "Confirm password is required";
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $confirm_password)) {
        $cperror = "Invalid password format";
    }

    // Validate Image
    $filesize = 3 * 1024 * 1024;
    $location = "profilepictures/";
    if ($picture['error'] == 0) {
        if ($picture['type'] == 'image/png' || $picture['type'] == 'image/jpg' || $picture['type'] == 'image/jpeg') {
            if ($picture['size'] <= $filesize) {
                $filename = uniqid('foodplace_') . "." . pathinfo($picture['name'], PATHINFO_EXTENSION);
                $filelocation = $location . $filename;
                echo $filelocation;
            } else {
                $picError = "File is too large";
            }
        } else {
            $picError = $picture['type'] . " not supported";
        }
    } else {
        $picError = "Upload Error";
    }

    // // Database Population
    if (empty($fnerror) && empty($lnerror) && empty($pherror) && empty($emerror) && empty($perror) && empty($cperror) && empty($aerror)) {
        $code = rand(100000, 999999);
        $pass = password_hash($password, PASSWORD_DEFAULT);
        echo $code;
        $query = "INSERT INTO users(firstname, lastname, phone, email, password, verification_code, allergies, profile_picture) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssss', $firstname, $lastname, $phone, $email, $pass, $code, $allergies, $filelocation);

        if ($stmt->execute()) {
            $name = $firstname . " " . $lastname;
            $link = "http://localhost/foodplace/verify.php?email=$email&token=$code";
            $mail->setFrom('6bytes@nhsurulere.site', 'Food Place');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Food Place Account Verification';
            $mail->Body = "<h1>Hello $name</h1>
            <p>Thank you for registering on our platform, below is your verification code or click the link to verify</p>
            <h1>$code</h1>
            <p>Please click <a href='$link'>here</a> to verify</p>
            <h6>Warm regard <br/> Food Place Team</h6>";

            $mail->AltBody = "Hello $name. Thank you for registering on our platform, this is your verification code $code or click the link to verify $link";

            $mail->send();

            move_uploaded_file($picture['tmp_name'], $filelocation);
            $msg = "Registered Successfully";
            header("Location: verify.php?email=$email");
            $picError = $fnerror = $lnerror = $pherror = $emerror = $perror = $cperror = $aerror = $msg = "";
            $firstname = $lastname = $phone = $email = $password = $confirm_password = $allergies = "";
        } else {
            $msg = "Registration Failed";
        }
    } else {
        $msg = "Registration Failed";
    }
}
?>

<section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-5">
        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
            Food Place
        </a>
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Create an account
                </h1>
                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    <?= $msg ?>
                </h1>
                <form class="space-y-4 md:space-y-6" action="" method="post" enctype="multipart/form-data">
                    <!-- Profile Picture Upload -->
                    <div class="flex flex-col items-center justify-center w-full">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="inline-block size-50 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5" id="display" />

                        <label class="block mb-2.5 text-sm font-medium text-heading" for="file_input">Upload file</label>
                        <input class="cursor-pointer w-full text-white bg-brand hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 shadow-xs placeholder:text-body" id="file_input" type="file" name="picture" accept="image/png, image/jpeg, image/jpg" />
                        <span class="text-red-600"><?= $picError ?></span>
                    </div>

                    <!-- Firstname Field -->
                    <div>
                        <label for="firstname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Firstname</label>
                        <input type="text" name="firstname" id="firstname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="John" value="<?= $firstname ?>" />
                        <span class="text-red-600"><?= $fnerror ?></span>
                    </div>
                    <!-- Lastname Field -->
                    <div>
                        <label for="lastname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Lastname</label>
                        <input type="text" name="lastname" id="lastname" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Doe" value="<?= $lastname ?>" />
                        <span class="text-red-600"><?= $lnerror ?></span>
                    </div>
                    <!-- Phone Number Field -->
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="08012345678 or +2348012345678" value="<?= $phone ?>" />
                        <span class="text-red-600"><?= $pherror ?></span>
                    </div>
                    <!-- E-mail Field -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                        <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" value="<?= $email ?>" />
                        <span class="text-red-600"><?= $emerror ?></span>
                    </div>
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= $password ?>" />
                        <span class="text-red-600"><?= $perror ?></span>
                    </div>
                    <!-- Confirm Password Field -->
                    <div>
                        <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= $confirm_password ?>" />
                        <span class="text-red-600"><?= $cperror ?></span>
                    </div>
                    <!-- Allergies Field -->
                    <div>
                        <label for="allergies" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Allergies</label>
                        <textarea name="allergies" id="allergies" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter allergies seperated with commas(,) Eg. (peanut butter, spaghetti, lactos intolerance)" value="<?= $allergies ?>"></textarea>
                        <span class="text-red-600"><?= $aerror ?></span>
                    </div>
                    <!-- Terms & Condition -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" aria-describedby="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-light text-gray-500 dark:text-gray-300">I accept the <a class="font-medium text-primary-600 hover:underline dark:text-primary-500" href="#">Terms and Conditions</a></label>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="submit" class="w-full text-white bg-brand hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Create an account</button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Already have an account? <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    const picture = document.getElementById('file_input');
    const display = document.getElementById('display');

    picture.addEventListener('change', (e) => {
        let file = e.target.files[0];
        console.log(file);
        if (file.type === "image/png" || file.type === "image/jpg" || file.type === "image/jpeg") {
            display.src = URL.createObjectURL(file);
            display.style.backgroundPosition = 'contain';
        } else {
            alert("File type not supported");
        }

    })
</script>