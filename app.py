from flask import Flask, render_template, request, redirect, url_for, session, flash, send_file
import sqlite3
from flask_bcrypt import Bcrypt
import os
from werkzeug.utils import secure_filename

app = Flask(__name__)
app.secret_key = "supersecretkey"
bcrypt = Bcrypt(app)

DB_NAME = "database.db"
UPLOAD_FOLDER = "static/profile_pics"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# sadece belirli kullanıcı admin paneli görebilsin
ALLOWED_ADMIN_USERNAME = "abdullah"  # burayı kendi kullanıcı adına göre değiştir

# --- Veritabanı oluşturma ---
def init_db():
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute('''CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        role TEXT NOT NULL,
                        username TEXT UNIQUE NOT NULL,
                        password TEXT NOT NULL,
                        name TEXT,
                        surname TEXT,
                        birthdate TEXT,
                        gender TEXT,
                        class TEXT,
                        subject TEXT,
                        phone TEXT,
                        school_number TEXT,
                        school_name TEXT,
                        gmail TEXT,
                        profile_picture TEXT
                    )''')
        conn.commit()

# --- Ana sayfa ---
@app.route("/")
def home():
    return redirect(url_for("login"))

# --- Register ---
@app.route("/register", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        role = request.form["role"]
        username = request.form["username"]
        password = request.form["password"]
        confirm_password = request.form["confirm_password"]

        if password != confirm_password:
            flash("Parolalar uyuşmuyor!")
            return redirect(url_for("register"))

        hashed_pw = bcrypt.generate_password_hash(password).decode("utf-8")

        # Ortak bilgiler
        name = request.form.get("name")
        surname = request.form.get("surname")
        birthdate = request.form.get("birthdate")
        gender = request.form.get("gender")

        # Öğrenci bilgileri
        class_ = request.form.get("class")
        phone = request.form.get("phone")
        school_number = request.form.get("school_number")
        school_name = request.form.get("school_name")
        gmail = request.form.get("gmail")

        # Öğretmen bilgileri
        subject = request.form.get("subject")

        # Profil fotoğrafı
        file = request.files.get("profile_picture")
        filename = None
        if file and file.filename != "":
            filename = secure_filename(file.filename)
            file.save(os.path.join(UPLOAD_FOLDER, filename))

        try:
            with sqlite3.connect(DB_NAME) as conn:
                c = conn.cursor()
                c.execute("""INSERT INTO users 
                    (role, username, password, name, surname, birthdate, gender, class, subject, phone, school_number, school_name, gmail, profile_picture)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)""",
                    (role, username, hashed_pw, name, surname, birthdate, gender, class_, subject, phone, school_number, school_name, gmail, filename))
                conn.commit()
            flash("Kayıt başarılı! Giriş yapabilirsiniz.")
            return redirect(url_for("login"))
        except sqlite3.IntegrityError:
            flash("Bu kullanıcı adı zaten alınmış.")
            return redirect(url_for("register"))

    return render_template("register.html")

# --- Login ---
@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        role = request.form["role"]
        username = request.form["username"]
        password = request.form["password"]

        with sqlite3.connect(DB_NAME) as conn:
            c = conn.cursor()
            c.execute("SELECT id, password, role FROM users WHERE username=?", (username,))
            user = c.fetchone()

        if user and bcrypt.check_password_hash(user[1], password) and user[2] == role:
            session["user_id"] = user[0]
            session["username"] = username
            session["role"] = role
            flash("Giriş başarılı!")
            return redirect(url_for("cursorai"))
        else:
            flash("Kullanıcı adı, parola veya rol hatalı.")
            return redirect(url_for("login"))

    return render_template("login.html")

# --- Dashboard ---
@app.route("/cursorai")
def cursorai():
    if "user_id" in session:
        return render_template("index.html")
    else:
        flash("Lütfen giriş yapın.")
        return redirect(url_for("login"))

@app.route("/olmpiyat")
def olmpiyat():
    if "user_id" in session:
        return render_template("olmpiyat.html")
    else:
        flash("Lütfen giriş yapın.")
        return redirect(url_for("login"))

# --- Kullanıcıları listele (sadece izinli kullanıcı) ---
@app.route("/admin/users")
def list_users():
    if "username" not in session or session["username"] != ALLOWED_ADMIN_USERNAME:
        flash("Yetkiniz yok.")
        return redirect(url_for("login"))

    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("""SELECT id, role, username, name, surname, birthdate, gender, class, subject,
                            phone, school_number, school_name, gmail, profile_picture
                     FROM users""")
        users = c.fetchall()

    return render_template("users.html", users=users)

# --- Logout ---
@app.route("/logout")
def logout():
    session.clear()
    flash("Çıkış yapıldı.")
    return redirect(url_for("login"))

if __name__ == "__main__":
    init_db()
    app.run(host="0.0.0.0", port=5000)
