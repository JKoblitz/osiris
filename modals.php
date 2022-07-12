<?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>

    <div class="modal modal-lg" id="login" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-400 mw-full">
                <a href="#" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h5 class="modal-title">Login</h5>

                <form action="<?= ROOTPATH ?>/user/login" method="POST">
                    <input type="hidden" name="redirect" value="<?= $_GET['redirect'] ?? $_SERVER['REQUEST_URI'] ?>">
                    <div class="form-group">
                        <label for="username">User name: </label>
                        <input class="form-control" id="username" type="text" name="username" placeholder="abc21" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password: </label>
                        <input class="form-control" id="password" type="password" name="password" placeholder="your windows password" required />
                    </div>
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit" />
                </form>
            </div>
        </div>
    </div>

<?php } ?>