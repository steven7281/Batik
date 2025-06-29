
      <div class="modal fade" id="ModalTambahUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah User</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form class="needs-validation" novalidate action="proses/proses_register.php" method="POST">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="floatingInput" placeholder="Your Name" name="nama" required>
                      <label for="floatingInput">Nama</label>
                      <div class="invalid-feedback">
                        Masukan Nama.
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <form action="">
                      <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="username" required>
                        <label for="floatingInput">Username</label>
                        <div class="invalid-feedback">
                          Masukan Username.
                        </div>
                      </div>
                  </div>
                </div>
                  <div class="col-lg-8">
                    <form action="">
                      <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="floatingInput" placeholder="08XXXXXXXXX" name="nohp">
                        <label for="floatingInput">No HP</label>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-floating mb-3">
                      <input type="Password" class="form-control" id="floatingInput" placeholder="Password"  name="password">
                      <label for="floatingPassword">Password</label>
                    </div>
                  </div>
                </div>
                <div class="form-floating">
                  <textarea class="form-control" id="" style="height: 100px" name="alamat"></textarea>
                  <label for="floatinginput">Alamat</label>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" name="input_user_validate" value="1234">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>