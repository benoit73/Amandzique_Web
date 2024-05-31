<!DOCTYPE html>
<html>
<head>
    <title>Page d'accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="ressources/css/style.css">
    <link rel="stylesheet" href="ressources/css/accueil_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>


    <div class="container-fluid h90">
        <div class="row h100">
            <div class="col-md-2 sidebar h100">
                <?php echo view('partials/menu_view'); ?>
            </div>
            <div class="col-md-10">
                <?php echo view('partials/accueil_view'); ?>
            </div>
        </div>
    </div>

    <div class="lecteur bg-dark">
            <?php echo view('partials/lecteur_view'); ?>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-J3A2LrP9+ez4Z7aoJHU0z4fzl8gPfQbjEV4W6BOgi13KoGkFSxAR8hLsIwX2jBgK" crossorigin="anonymous"></script>
</body>
</html>
