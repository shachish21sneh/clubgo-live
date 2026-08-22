<style>
	.container { width: min(1100px, 92vw); margin: 0 auto; }
	footer { border-top: 1px solid #e5e7eb; margin-top: 32px; }
    .foot { padding: 22px 0; display: grid; gap: 10px; text-align: center; }
    .foot-links { flex-wrap: wrap; gap: 12px; font-size: 14px; color: var(--muted); }
    .copyright { color: var(--muted); font-size: 14px; }
</style>
<footer>
    <div class="container foot">
      <nav class="foot-links">
        <a href="#">Home</a>
        <span>•</span>
        <a href="#">About Us</a>
        <span>•</span>
        <a href="#">Contact Us</a>
        <span>•</span>
        <a href="#">Terms and Conditions</a>
        <span>•</span>
        <a href="#">Privacy Policy</a>
        <span>•</span>
        <a href="#">Request Delete Account</a>
      </nav>
      <div class="copyright">© 2025 ClubGo. All rights reserved.</div>
    </div>
  </footer>
<?php 
 include 'include/eventconfig.php';
 echo $validate['data'];
 ?>
