<?php
$color_choices = [
  'orange' => '#fb8c00',
  'teal' => '#00897b',
  'purple' => '#8e24aa',
  'blue' => '#1e88e5',
  'red' => '#e53935',
];

$style_color = $color_choices[ env('STYLE_COLOR', 'teal') ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e(config('app.name')); ?> | Example Laravel-Keycloak SSO Integration</title>
    <link rel="icon" href="https://dummyimage.com/70/<?php echo e(substr($style_color,1)); ?>/000&text=<?php echo e(config('app.name')); ?>">

     <!-- Pico.css (Classless version) -->
    <link rel="stylesheet" href="<?php echo e(asset('pico.classless.min.css')); ?>">

    <style>
/* Orange Light scheme (Default) */
/* Can be forced with data-theme="light" */
[data-theme="light"],
:root:not([data-theme="dark"]) {
  --primary: <?php echo e($style_color); ?>;
  --primary-hover: <?php echo e($style_color); ?>;
  --primary-focus: <?php echo e($style_color); ?>;
  --primary-inverse: #FFF;
}

article {
    margin:0;
    padding:1rem;
}

    </style>
</head>
<body style="background:var(--primary)">
 <!-- Header -->
  <header style="background:white; padding:1rem">
      <hgroup>
        <h1>Welcome to: <?php echo e(config('app.name')); ?></h1>
        <h3>Example Laravel-Keycloak SSO Integration</h3>
      </hgroup>
  </header>
  <main>
    <article>
        <details open="">
            <summary>Penjelasan mengenai web ini</summary>
            <p>
                Website ini adalah simulasi dari website-website Pemprov Jabar setelah melakukan integrasi dengan sistem SSO Jabar & web SIAP.
            </p>
            <p>
                Beberapa asumsi yang digunakan dalam implementasi web ini:
            </p>

            <ol>
                <li>SIAP akan menjadi "source of truth" terkait data kepegawaian Pemprov Jabar untuk aplikasi ini</li>
                <li>Untuk data pegawai selain menggunakan data dari SIAP, di sini akan disimulasikan bahwa ada juga data pegawai yang sudah ada di aplikasi, dan data tsb dapat disandingkan dengan data dari SIAP Jabar.</li>
            </ol>
        </details>
    </article>

    <br>

    <section style="display:flex; gap:1rem;">
        <article style="width:40%">
            <?php if(auth()->guard()->check()): ?>
            <strong>Selamat datang, <?php echo e(Auth::user()->name); ?></strong>
            <p>
                <button onclick="window.location='<?php echo e(route('oauth.logout')); ?>'">
                    Logout from all application in this session
                </button>
            </p>
            <?php else: ?>
            <strong>Anda belum login. Silahkan login terlebih dahulu</strong>
            <p>
                <button onclick="window.location='<?php echo e(route('oauth.login')); ?>'">
                    Login with Jabar SSO
                </button>
            </p>
            <?php endif; ?>

            <details>
                <summary><strong>Laravel/PHP Session ID</strong></summary>
                <p>Ini adalah ID dari sesi bawaan dari PHP. Setiap client yang mengunjungi web ini akan memiliki Session ID unik, yang nantinya ID ini bisa digunakan untuk proses "backchannel logout" dari Keycloak. Info lebih lanjut bisa cek referensi berikut:</p>
                <ul>
                    <li><a target="_blank" href="https://www.php.net/manual/en/features.session.security.management.php">Dokumentasi terkait manajemen Session di PHP</a></li>
                    <li><a target="_blank" href="https://stackoverflow.com/questions/56863876/how-to-logout-user-from-specific-session-in-laravel/56864262#56864262">Diskusi stackoverflow terkait logout session Laravel secara remote</a></li>
                </ul>
            </details>
            <p>
                <?php echo e(\Session::getId()); ?>

            </p>
            <details>
                <summary><strong>Keycloak Session ID</strong></summary>
                <p>Ini adalah ID dari sesi login yang digenerate oleh Keycloak. Guna ID ini agar ketika logout, website ini bisa mencocokkan sesi ID dari laravel yang perlu dihapuskan berdasarkan request logout dari server Keycloak. Info lebih lanjut bisa cek source code di <code>App\Http\Controllers\OAuthController.php</code> di metode <code>logoutWebhook()</code></p>
            </details>
            <p>
                <?php echo e(getCurrentKeycloakSessionId() ?: '-'); ?>

            </p>
        </article>

        <article style="width:60%">
            <h2>Detail terkait user saat ini:</h2>

            <?php if(auth()->guard()->check()): ?>
            <p><strong>NIK user yang didapat dari keycloak: <?php echo e(session('nik')); ?></strong></p>
            <h3>
                Data user yang sesuai dari API SIAP
            </h3>
            <pre>
        <?php echo e(print_r(getCurrentUserProfileFromSIAP())); ?>

            </pre>

            <h3>Data user dari keycloak</h3>

            <details open="">
                <summary>Penjelasan:</summary>
                <p>
                    Selain username, password, dan NIK, Keycloak juga menyimpan beberapa informasi lain yang ikut dikirimkan oleh server SSO ketika login: nama depan, nama belakang, serta email. Namun karena beberapa data ini beririsan dengan data yang disimpan di SIAP, disarankan agar menggunakan data dari SIAP saja agar menjadi 1 sumber "source of truth".
                </p>
            </details>
            <details>
                <summary>
                    <strong>
                        Tampilkan data user dari Keycloak
                    </strong>
                </summary>

                <pre>
        <?php echo e(print_r(session('KEYCLOAK_USER_DATA'))); ?>

                </pre>
            </details>

            <h3>Data user yang sesuai dari database app ini</h3>

            <details open="">
                <summary>Penjelasan:</summary>
                <p>
                    Walaupun data profil bisa dikirimkan oleh data SIAP, tetap dimungkinkan untuk menyimpan data profil terkait user di dalam aplikasi/website pemerintahan, khususnya jika data yang dibutuhkan tidak tersedia di SIAP. Salah satu manfaat lain dari penyimpanan data di database aplikasi/website ini adalah untuk membedakan hak akses/permission dari suatu user hanya di aplikasi/website tersebut saja. Contoh user kepala BKD di SIAP bisa jadi memiliki hak akses admin, namun user tersebut di aplikasi mungkin saja memiliki role yang berbeda tergantung kebijakan pengelola aplikasi/website ybs.
                </p>
            </details>
            <details>
                <summary>
                    <strong>
                        Tampilkan data user yang sesuai dari database app ini
                    </strong>
                </summary>

                <pre>
                <?php
                $localUserData = \App\Models\User::where('nik', session('nik'))->first();
                $localUserData = $localUserData ? $localUserData->toArray() : null;
                echo print_r($localUserData) ;
                ?>
                </pre>
            </details>
            <?php else: ?>
            <p>Not logged in</p>
            <?php endif; ?>
        </article>
    </section>

  </main>
</body>
</html>
<?php /**PATH /home/god/example-laravel-keycloak-sso/resources/views/welcome.blade.php ENDPATH**/ ?>