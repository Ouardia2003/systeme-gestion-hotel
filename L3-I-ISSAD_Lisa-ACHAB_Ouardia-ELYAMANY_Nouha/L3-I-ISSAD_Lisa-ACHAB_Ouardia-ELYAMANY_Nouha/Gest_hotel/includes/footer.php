<?php
?>
</div><footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted"><?= date('Y'); ?></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
if (isset($db) && $db) {
    pg_close($db);
}
?>