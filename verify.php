<?php
// Adding pagetitle, header and database connection
$pagetitle = "Email Account Verification";
require_once "assets/header.php";
require_once "assets/db_connect.php";

// Variable initialization
$msg = $token = $email = $tkerror = "";

// Redirecting when email is not valid
if(isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    header("Location: register.php");
}

// Capturing token from url parameter
if(isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ? AND email = ?");
    $stmt->bind_param('ss', $token, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1) {
       $now = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE users SET verified_at = ?, verification_code = NULL WHERE email = ?");
        $stmt->bind_param('ss', $now, $email);
        if($stmt->execute()) {
            $msg = "Verification Successful";
        } else {
            $msg = "Verification Failed";
        }
    } else {
        $msg = "No record Found";
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
                    <?= $pagetitle ?>
                </h1>
                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    <?= $msg ?>
                </h1>
                <form class="space-y-4 md:space-y-6" action="" method="get">
                    <!-- Terms & Condition -->
                    <div class="flex items-start">

                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-light text-gray-500 dark:text-gray-300">A code is sent to: <span class="font-medium text-primary-600 hover:underline dark:text-primary-500"><?= $email ?></span></label>
                        </div>
                    </div>


                    <!-- token Number Field -->
                    <div>
                        <input type="hidden" name="email" value="<?= $email ?>">
                        <label for="token" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your token Number</label>
                        <input type="number" name="token" id="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="123456" value="<?= $token ?>" />
                        <span class="text-red-600"><?= $tkerror ?></span>
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