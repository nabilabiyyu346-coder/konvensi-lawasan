<form method="post" action="upload_bukti.php" enctype="multipart/form-data" class="upload-form">
  <input type="hidden" name="pesanan_id" value="<?= $pesanan['id'] ?>">
  <label for="bukti">Foto/screenshot bukti transfer</label>
  <input type="file" id="bukti" name="bukti" accept="image/jpeg,image/png,image/webp" required>
  <button type="submit" class="btn btn--solid btn--small">Unggah Bukti</button>
</form>
