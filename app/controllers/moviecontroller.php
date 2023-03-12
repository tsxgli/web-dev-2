<?php
namespace Controllers;

use Exception;
use Services\MovieService;

class MovieController extends Controller
{
    private $movieservice;

    function __construct()
    {
        $this->movieservice = new MovieService();
    }
    public function getAll()
    {
        $offset = NULL;
        $limit = NULL;

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }

        $movies = $this->movieservice->getAll($offset, $limit);
        $this->respond($movies);
    }
    public function showTop250Movies()
    {
        require __DIR__ . '/../views/api/topmovies.php';
    }
    public function filterMovies(string $filter)
    {
        $model = $this->movieservice->filterMovies($filter);
        $_SESSION['genre'] = $filter;
        require __DIR__ . '/../views/home/genre.php';
    }
    public function getMovie()
    {
        $id = $_GET['id'];
        $model = $this->movieservice->getMovie($id);
        require __DIR__ . '/../views/home/detail.php';
    }

    public function manageMovies()
    {
        $model = $this->movieservice->getAll();
        require __DIR__ . '/../views/admin/moviesmanagement.php';
    }
    public function deleteMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'];

            $success = $this->movieservice->deleteMovie($id);
            if ($success) {
                http_response_code(200);
                echo json_encode(["movies" => $this->movieservice->getAll()]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Failed to delete movie."]);
            }
        } else {
            http_response_code(405);
        }
    }

    public function editMovie()
    {
        $id = $_GET['id'];
        $model = $this->movieservice->getMovie($id);
        require __DIR__ . '/../views/admin/editmovie.php';
    }

    public function showCartItems()
    {
        require __DIR__ . '/../views/order/index.php';
    }

    public function addMovieToCart()
    {
        $movieId = $_GET['id'];
        $movie = $this->movieservice->getMovie($movieId);

        $_SESSION['cartItems'] = $movie;

        require __DIR__ . '/../views/order/index.php';
    }

    public function updateMovie()
    {
        if (isset($_POST['updateMovieBtn'])) {
            $ImageName = $this->movePicture($_FILES['imageSelector']);
            
            $id = htmlspecialchars($_POST['editId']);
            $title = htmlspecialchars($_POST['editTitle']);
            $description = htmlspecialchars($_POST['editDescription']);
            $director = htmlspecialchars($_POST['editDirector']);
            $dateProduced = htmlspecialchars(htmlspecialchars($_POST['editDateProduced']));
            $rating = htmlspecialchars($_POST['editRating']);
            $price = htmlspecialchars($_POST['editPrice']);
            $genre = htmlspecialchars($_POST['editGenre']);
            if(!isset($_POST['imageSelector'])){
                $image = $_POST['editImage'];
            }else{
                $image = $ImageName;
            }
            $this->movieservice->updateMovie($id, $title, $description, $genre, $rating, $dateProduced, $price, $director, $image);

        }
    }

    public function addMovieIndex()
    {
        require __DIR__ . '/../views/admin/addmovie.php';
    }
    public function addMovie()
    {
        // A list of permitted file extensions
        if (isset($_POST['addMovieBtn'])) {
            $newImageName = $this->movePicture($_FILES['addImage']);
            $data = array(
                'title' => htmlspecialchars($_POST['addTitle']),
                'description' => (htmlspecialchars($_POST['addDescription'])),
                'dateProduced' => htmlspecialchars($_POST['addDateProduced']),
                'director' => htmlspecialchars($_POST['addDirector']),
                'genre' => htmlspecialchars($_POST['addGenre']),
                'rating' => htmlspecialchars($_POST['addRating']),
                'price' => htmlspecialchars($_POST['addPrice']),
                'image' => ($newImageName),
                'stock' => (100),
            );
            $this->movieservice->addMovie($data);
            echo "<script>location.href='/admin/managemovies'</script>";
            //exit;
        }

    }
    public function movePicture($imageName)
    {
        $fileName = $imageName['name'];
        $tempName = $imageName['tmp_name'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newImageName = uniqid() . '.' . $ext;

        if (isset($fileName)) {
            if (!empty($fileName)) {
                $location = "images/";
                if (move_uploaded_file($tempName, $location . $newImageName)) {
                    echo 'File Uploaded';
                }
            }
        }
        return $newImageName;
    }
}