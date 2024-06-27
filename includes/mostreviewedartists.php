<?php

include __DIR__ . '/../repositories/artistRepository.php';
$db = new Database();

$artistRepo = new artistRepository($db);


try {

    // Retrieve three most reviewed artists
    $artists = $artistRepo->threeMostReviewedArtists();

} catch (Exception $e) {
    echo "Error fetching artworks: " . htmlspecialchars($e->getMessage());
    exit;
}

$parentFolderName = basename(dirname(dirname(__FILE__)));
?>


<div class="container_home">
  
        <h2 class="font-weight-bold text-center mb-3">Most reviewed artists</h2>
        <p class="font-italic text-muted text-center mb-5">Experience the impact of creativity with our most reviewed artists.</p>

          <div class="row justify-content-center">
              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <!-- Card-->
                <div class="card shadow-sm border-0 rounded">
                  <div class="card-body p-0">
                  <img src="<?= getCorrectImagePath('../assets/images/artists/medium/', ($artists[0]->getArtistID())); ?>" 
                  title="<?= htmlspecialchars($artists[0]->getFirstName() . ' ' . $artists[0]->getLastName()) ?>" 
                  alt="<?= htmlspecialchars($artists[0]->getFirstName() . ' ' . $artists[0]->getLastName()) ?>"
                  class="img-fluid"/>

                    <div class="p-4">
                      <h5 class="mb-0"><?= htmlspecialchars($artists[0]->getFirstName() . ' ' . $artists[0]->getLastName()) ?></h5>
                      <p class="small text-muted">
                          <a href="<?= htmlspecialchars($artists[0]->getArtistLink()) ?>">Wikipedia Link</a>
                      </p>


                      <ul class="social mb-0 list-inline mt-3">
              <li class="list-inline-item m-0">
                  <small class="text-muted">
                      <i class="fas fa-eye pr-2"></i> <!-- Padding right added -->
                      <span class="ml-2"><?= htmlspecialchars($artists[0]->getReviewCount()) ?></span>
                  </small>
              </li>
              <li class="list-inline-item ml-1">
                  <small class="text-muted">
                      <i class="fas fa-globe pr-2"></i> <!-- Padding right added -->
                      <span class="ml-2"><?= htmlspecialchars($artists[0]->getNationality()) ?></span>
                  </small>
              </li>
              <li class="list-inline-item ml-1">
                  <small class="text-muted">
                      <i class="fas fa-hourglass-end pr-2"></i> <!-- Padding right added -->
                      <span class="ml-2">
                          <?= htmlspecialchars($artists[0]->getYearOfBirth()) ?> - 
                          <?= htmlspecialchars($artists[0]->getYearOfDeath()) ?: 'Present' ?>
                      </span>
                  </small>
              </li>
          </ul>

                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <!-- Card-->
                <div class="card shadow-sm border-0 rounded">
                  <div class="card-body p-0"> <img src="<?= getCorrectImagePath('../assets/images/artists/medium/', ($artists[1]->getArtistID())); ?>" title="<?= htmlspecialchars($artists[1]->getFirstName() . ' ' . $artists[0]->getLastName()) ?>" alt="<?= htmlspecialchars($artists[1]->getFirstName() . ' ' . $artists[1]->getLastName()) ?>" class="img-fluid"/>
                    <div class="p-4">
                      <h5 class="mb-0"><?= htmlspecialchars($artists[1]->getFirstName() . ' ' . $artists[1]->getLastName()) ?></h5>
                      <p class="small text-muted">
                          <a href="<?= htmlspecialchars($artists[1]->getArtistLink()) ?>">Wikipedia Link</a>
                      </p>

                      <ul class="social mb-0 list-inline mt-3">
              <li class="list-inline-item m-0">
                  <small class="text-muted">
                      <i class="fas fa-eye pr-2"></i>
                      <span class="ml-2"><?= htmlspecialchars($artists[1]->getReviewCount()) ?></span>
                  </small>
              </li>
              <li class="list-inline-item ml-1">
                  <small class="text-muted">
                      <i class="fas fa-globe pr-2"></i>
                      <span class="ml-2"><?= htmlspecialchars($artists[1]->getNationality()) ?></span>
                  </small>
              </li>
              <li class="list-inline-item ml-1">
                  <small class="text-muted">
                      <i class="fas fa-hourglass-end pr-2"></i>
                      <span class="ml-2">
                          <?= htmlspecialchars($artists[1]->getYearOfBirth()) ?> - 
                          <?= htmlspecialchars($artists[1]->getYearOfDeath()) ?: 'Present' ?>
                      </span>
                  </small>
              </li>
          </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <!-- Card-->
                <div class="card shadow-sm border-0 rounded">
                  <div class="card-body p-0"> <img src="<?= getCorrectImagePath('../assets/images/artists/medium/', ($artists[2]->getArtistID())); ?>" title="<?= htmlspecialchars($artists[2]->getFirstName() . ' ' . $artists[2]->getLastName()) ?>" alt="<?= htmlspecialchars($artists[2]->getFirstName() . ' ' . $artists[2]->getLastName()) ?>" class="img-fluid"/>
                    <div class="p-4">
                      <h5 class="mb-0"><?= htmlspecialchars($artists[2]->getFirstName() . ' ' . $artists[2]->getLastName()) ?></h5>
                      <p class="small text-muted">
                          <a href="<?= htmlspecialchars($artists[2]->getArtistLink()) ?>">Wikipedia Link</a>
                      </p>

                      <ul class="social mb-0 list-inline mt-3">
                          <li class="list-inline-item m-0">
                              <small class="text-muted">
                                  <i class="fas fa-eye pr-2"></i>
                                  <span class="ml-2"><?= htmlspecialchars($artists[2]->getReviewCount()) ?></span>
                              </small>
                          </li>
                          <li class="list-inline-item ml-1">
                              <small class="text-muted">
                                  <i class="fas fa-globe pr-2"></i>
                                  <span class="ml-2"><?= htmlspecialchars($artists[2]->getNationality()) ?></span>
                              </small>
                          </li>
                          <li class="list-inline-item ml-1">
                              <small class="text-muted">
                                  <i class="fas fa-hourglass-end pr-2"></i>
                                  <span class="ml-2">
                                      <?= htmlspecialchars($artists[2]->getYearOfBirth()) ?> - 
                                      <?= htmlspecialchars($artists[2]->getYearOfDeath()) ?: 'Present' ?>
                                  </span>
                              </small>
                          </li>
                         </ul>
                    </div>
                  </div>
                </div>
              </div>
        </div>
      
    </div>  
    <div>