<?php
    require 'function.php';
    require 'cek.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">Laundy Labs</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">UTAMA</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Pelanggan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Data Pelanggan</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Laundry
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">

                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="tables.html">Data Laundry</a>
                                </nav>

                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Status Laundry</a>
                                </nav>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>

                            <a class="nav-link" href="logout.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                                Logout
                            </a>

                            
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?= $_SESSION['nama_user']; ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laundry's</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Primary Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4">
                                    <div class="card-body">Warning Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">Success Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">Danger Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                         <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">Data Operasional</h6>
                                <button type="button"
                                        class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#myModal">
                                        <i class="fas fa-plus"></i>
                                    Tambah Data
                                </button>
                            </div>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple" class="table table-bordered table-striped">

                                    <thead> 
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>No HP</th>
                                            <th>Jenis Pencucian</th>
                                            <th>Jenis Layanan</th>
                                            <th>Tanggal Penjemputan</th>
                                            <th>Jam Penjemputan</th>
                                            <th>Kode Order</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        
                                        <?php
                                        $i = 1;
                                        $ambilsemuadatatransaksi= mysqli_query($conn, "SELECT * FROM transaksi");

                                        while ($data = mysqli_fetch_assoc($ambilsemuadatatransaksi)) {
                                        ?>
                                            <tr>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $data['nama']; ?></td>
                                                    <td><?= $data['no_hp']; ?></td>
                                                    <td><?= $data['jenis_pencucian']; ?></td>
                                                    <td><?= $data['jenis_layanan']; ?></td>
                                                    <td><?= $data['tanggal_penjemputan']; ?></td>
                                                    <td><?= $data['jam_penjemputan']; ?></td>
                                                    <td><?= $data['kode_order']; ?></td>
                                                    <td><?= $data['status']; ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2 justify-content-end">

                                                            <!-- Tombol Edit -->
                                                            <a href="edit_operasional.php?id=<?= $data['id_laundry']; ?>"
                                                            class="btn btn-success btn-sm d-flex align-items-center gap-1 px-3">
                                                            
                                                            <i class="bi bi-pencil-square"></i>
                                                            Edit
                                                            </a>

                                                            <!-- Tombol Hapus -->
                                                            <a href="hapus_operasional.php?id=<?= $data['id_laundry']; ?>"
                                                            class="btn btn-danger btn-sm d-flex align-items-center gap-1 px-3"
                                                            onclick="return confirm('Yakin hapus data ini?')">
                                                            
                                                            <i class="bi bi-trash"></i>
                                                            Hapus
                                                            </a>

                                                    </div>
                                                    </td>



                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                        
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </body>
    
        <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
<form method="post">
    <div class="row g-3">

        <!-- Nama -->
        <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama pelanggan" required>
        </div>

        <!-- No HP -->
        <div class="col-md-6">
            <label class="form-label">No HP</label>
            <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789" required>
        </div>

        <!-- Jenis Pencucian -->
        <div class="col-md-6">
            <label class="form-label">Jenis Pencucian</label>
            <select name="jenis_pencucian" class="form-select" required>
                <option value="Cuci Kering">Cuci Kering</option>
                <option value="Cuci Setrika">Cuci Setrika</option>
            </select>

        </div>

        <!-- Jenis Layanan -->
        <div class="col-md-6">
            <label class="form-label">Jenis Layanan</label>
            <select name="jenis_layanan" class="form-select" required>
                <option value="Reguler">Reguler</option>
                <option value="Express">Express</option>
            </select>

        </div>

        <!-- Tanggal Penjemputan -->
        <div class="col-md-6">
            <label class="form-label">Tanggal Penjemputan</label>
            <input type="date" name="tanggal_penjemputan" class="form-control" required>
        </div>

        <!-- Jam Penjemputan -->
        <div class="col-md-6">
            <label class="form-label">Jam Penjemputan</label>
            <input type="time" name="jam_penjemputan" class="form-control" required>
        </div>

        <!-- Tombol -->
        <div class="col-12 text-end">
            <button type="submit" name="simpan" class="btn btn-primary">
                Simpan Data
            </button>
        </div>

    </div>
</form>




       
            
    </div>
            

            
            </div>
        </div>
    </div>
</div>
</html>
