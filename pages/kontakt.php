<section id="kontakt">
    <h2>Kontaktirajte nas</h2>

    <div class="contact-form">
        <form action="#" method="post" class="form-wide">
            <label for="ime">Ime*</label>
            <input type="text" id="ime" name="ime" required>

            <label for="prezime">Prezime*</label>
            <input type="text" id="prezime" name="prezime" required>

            <label for="email">E-mail adresa*</label>
            <input type="email" id="email" name="email" required>

            <label for="drzava">Država</label>
            <select id="drzava" name="drzava">
                <option value="hr">Hrvatska</option>
                <option value="si">Slovenija</option>
                <option value="rs">Srbija</option>
                <option value="ba">Bosna i Hercegovina</option>
                <option value="at">Austrija</option>
                <option value="de">Njemačka</option>
            </select>

            <label for="opis">Opis</label>
            <textarea id="opis" name="opis" rows="4"></textarea>

            <button type="submit">Pošalji</button>
        </form>
    </div>
</section>

<div class="map-full">
    <h2>Naša lokacija</h2>

    <p>
        <a href="https://www.google.com/maps?q=Zagreb" target="_blank" rel="noopener">
            Otvori lokaciju u Google Kartama
        </a>
    </p>

    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2781.028402478417!2d15.981919315718593!3d45.81501007910644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4765d6fcbf5e9b17%3A0x5d2c8f1c5b8e8e1f!2sZagreb!5e0!3m2!1shr!2shr!4v1700000000000"
        width="100%"
        height="450"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>
