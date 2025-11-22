from flask import Flask, render_template, request, redirect, url_for, session, flash, send_file
import sqlite3
from flask_bcrypt import Bcrypt
import os
from werkzeug.utils import secure_filename

import csv
from flask import Response

app = Flask(__name__)
app.secret_key = "supersecretkey"
bcrypt = Bcrypt(app)

DB_NAME = "database.db"
UPLOAD_FOLDER = "static/profile_pics"
DOCUMENTS_FOLDER = "static/documents"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(DOCUMENTS_FOLDER, exist_ok=True)

# Admin kullanıcıları listesi
ALLOWED_ADMIN_USERNAMES = ["AbdullahAdrainMorsy", "DogruHoca"]

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
        
        # Videolar tablosu
        c.execute('''CREATE TABLE IF NOT EXISTS videos (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT NOT NULL,
                        youtube_url TEXT NOT NULL,
                        description TEXT,
                        order_index INTEGER DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )''')
        
        # Dökümanlar tablosu
        c.execute('''CREATE TABLE IF NOT EXISTS documents (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT NOT NULL,
                        description TEXT,
                        file_path TEXT NOT NULL,
                        order_index INTEGER DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )''')
        
        # Faydalı linkler tablosu
        c.execute('''CREATE TABLE IF NOT EXISTS links (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT NOT NULL,
                        url TEXT NOT NULL,
                        order_index INTEGER DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )''')
        
        # Site bilgileri tablosu
        c.execute('''CREATE TABLE IF NOT EXISTS site_info (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        key TEXT UNIQUE NOT NULL,
                        value TEXT
                    )''')
        
        # Varsayılan site bilgilerini ekle
        c.execute("INSERT OR IGNORE INTO site_info (key, value) VALUES ('about_text', 'Merhaba, Ben Doğru Hoca. [Buraya kendi özgeçmişinizi ve deneyimlerinizi ekleyebilirsiniz]')")
        c.execute("INSERT OR IGNORE INTO site_info (key, value) VALUES ('contact_email', 'dogrumehmet@gmail.com')")
        c.execute("INSERT OR IGNORE INTO site_info (key, value) VALUES ('contact_phone', '+90 505 781 07 60')")
        c.execute("INSERT OR IGNORE INTO site_info (key, value) VALUES ('contact_address', 'İstanbul, Türkiye')")
        
        conn.commit()

# bu database silmeden yeni sutunlar eklemek icin
# def migrate_users_table():
#     with sqlite3.connect(DB_NAME) as conn:
#         c = conn.cursor()
#         try:
#             c.execute("ALTER TABLE users ADD COLUMN phone TEXT;")
#             c.execute("ALTER TABLE users ADD COLUMN school_number TEXT;")
#             c.execute("ALTER TABLE users ADD COLUMN school_name TEXT;")
#             c.execute("ALTER TABLE users ADD COLUMN gmail TEXT;")
#             c.execute("ALTER TABLE users ADD COLUMN profile_picture TEXT;")
#             conn.commit()
#             print("Users tablosu güncellendi.")
#         except sqlite3.OperationalError:
#             print("Sütunlar zaten var, atlandı.")
# --- Ana sayfa ---
@app.route("/")
def home():
    return render_template("index.html")

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
        fullname = request.form.get("fullname", "")
        gender = request.form.get("gender")

        # Öğrenci bilgileri
        class_ = request.form.get("class")
        phone = request.form.get("phone")
        school_name = request.form.get("school_name")
        gmail = request.form.get("gmail")

        # Öğretmen bilgileri
        subject = request.form.get("subject")

        try:
            with sqlite3.connect(DB_NAME) as conn:
                c = conn.cursor()
                c.execute("""INSERT INTO users 
                    (role, username, password, name, surname, birthdate, gender, class, subject, phone, school_number, school_name, gmail, profile_picture)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)""",
                    (role, username, hashed_pw, fullname, None, None, gender, class_, subject, phone, None, school_name, gmail, None))
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
    return render_template("index.html")


@app.route("/olmpiyat")
def olmpiyat():
    return render_template("olmpiyat.html")

# ============= ADMIN DASHBOARD =============
# Helper function to check admin access
def is_admin():
    return "username" in session and session["username"] in ALLOWED_ADMIN_USERNAMES

# --- Admin Dashboard Ana Sayfa ---
@app.route("/admin/dashboard")
def admin_dashboard():
    if not is_admin():
        flash("Yetkiniz yok.")
        return redirect(url_for("admin_login"))
    return render_template("dashboard.html")

# --- Admin Login ---
@app.route("/admin/login", methods=["GET", "POST"])
def admin_login():
    if request.method == "POST":
        username = request.form["username"]
        password = request.form["password"]
        
        # Admin kullanıcı adı kontrolü
        if username not in ALLOWED_ADMIN_USERNAMES:
            flash("Bu kullanıcı admin değil.")
            return redirect(url_for("admin_login"))
        
        with sqlite3.connect(DB_NAME) as conn:
            c = conn.cursor()
            c.execute("SELECT id, password FROM users WHERE username=?", (username,))
            user = c.fetchone()
        
        if user and bcrypt.check_password_hash(user[1], password):
            session["user_id"] = user[0]
            session["username"] = username
            session["role"] = "admin"
            flash("Admin girişi başarılı!")
            return redirect(url_for("admin_dashboard"))
        else:
            flash("Kullanıcı adı veya parola hatalı.")
            return redirect(url_for("admin_login"))
    
    return render_template("admin-login.html")

# --- VIDEO MANAGEMENT API ---
@app.route("/api/videos", methods=["GET"])
def get_videos():
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("SELECT id, title, youtube_url, description, order_index FROM videos ORDER BY order_index, id")
        videos = [{"id": row[0], "title": row[1], "youtube_url": row[2], "description": row[3], "order_index": row[4]} for row in c.fetchall()]
    return {"videos": videos}

@app.route("/api/videos", methods=["POST"])
def add_video():
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    data = request.get_json()
    title = data.get("title")
    youtube_url = data.get("youtube_url")
    description = data.get("description", "")
    order_index = data.get("order_index", 0)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("INSERT INTO videos (title, youtube_url, description, order_index) VALUES (?, ?, ?, ?)",
                  (title, youtube_url, description, order_index))
        conn.commit()
        video_id = c.lastrowid
    
    return {"success": True, "id": video_id}

@app.route("/api/videos/<int:video_id>", methods=["PUT"])
def update_video(video_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    data = request.get_json()
    title = data.get("title")
    youtube_url = data.get("youtube_url")
    description = data.get("description", "")
    order_index = data.get("order_index", 0)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("UPDATE videos SET title=?, youtube_url=?, description=?, order_index=? WHERE id=?",
                  (title, youtube_url, description, order_index, video_id))
        conn.commit()
    
    return {"success": True}

@app.route("/api/videos/<int:video_id>", methods=["DELETE"])
def delete_video(video_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("DELETE FROM videos WHERE id=?", (video_id,))
        conn.commit()
    
    return {"success": True}

# --- DOCUMENT MANAGEMENT API ---
@app.route("/api/documents", methods=["GET"])
def get_documents():
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("SELECT id, title, description, file_path, order_index FROM documents ORDER BY order_index, id")
        documents = [{"id": row[0], "title": row[1], "description": row[2], "file_path": row[3], "order_index": row[4]} for row in c.fetchall()]
    return {"documents": documents}

@app.route("/api/documents", methods=["POST"])
def add_document():
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    title = request.form.get("title")
    description = request.form.get("description", "")
    order_index = request.form.get("order_index", 0)
    
    file = request.files.get("file")
    if not file or file.filename == "":
        return {"error": "Dosya gerekli"}, 400
    
    filename = secure_filename(file.filename)
    file_path = os.path.join(DOCUMENTS_FOLDER, filename)
    file.save(file_path)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("INSERT INTO documents (title, description, file_path, order_index) VALUES (?, ?, ?, ?)",
                  (title, description, f"documents/{filename}", order_index))
        conn.commit()
        doc_id = c.lastrowid
    
    return {"success": True, "id": doc_id}

@app.route("/api/documents/<int:doc_id>", methods=["PUT"])
def update_document(doc_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    title = request.form.get("title")
    description = request.form.get("description", "")
    order_index = request.form.get("order_index", 0)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        
        # Eğer yeni dosya yüklenmişse
        file = request.files.get("file")
        if file and file.filename != "":
            # Eski dosyayı sil
            c.execute("SELECT file_path FROM documents WHERE id=?", (doc_id,))
            old_file = c.fetchone()
            if old_file:
                old_path = os.path.join("static", old_file[0])
                if os.path.exists(old_path):
                    os.remove(old_path)
            
            # Yeni dosyayı kaydet
            filename = secure_filename(file.filename)
            file_path = os.path.join(DOCUMENTS_FOLDER, filename)
            file.save(file_path)
            
            c.execute("UPDATE documents SET title=?, description=?, file_path=?, order_index=? WHERE id=?",
                      (title, description, f"documents/{filename}", order_index, doc_id))
        else:
            c.execute("UPDATE documents SET title=?, description=?, order_index=? WHERE id=?",
                      (title, description, order_index, doc_id))
        
        conn.commit()
    
    return {"success": True}

@app.route("/api/documents/<int:doc_id>", methods=["DELETE"])
def delete_document(doc_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        
        # Dosyayı sil
        c.execute("SELECT file_path FROM documents WHERE id=?", (doc_id,))
        file_info = c.fetchone()
        if file_info:
            file_path = os.path.join("static", file_info[0])
            if os.path.exists(file_path):
                os.remove(file_path)
        
        c.execute("DELETE FROM documents WHERE id=?", (doc_id,))
        conn.commit()
    
    return {"success": True}

# --- LINKS MANAGEMENT API ---
@app.route("/api/links", methods=["GET"])
def get_links():
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("SELECT id, title, url, order_index FROM links ORDER BY order_index, id")
        links = [{"id": row[0], "title": row[1], "url": row[2], "order_index": row[3]} for row in c.fetchall()]
    return {"links": links}

@app.route("/api/links", methods=["POST"])
def add_link():
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    data = request.get_json()
    title = data.get("title")
    url = data.get("url")
    order_index = data.get("order_index", 0)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("INSERT INTO links (title, url, order_index) VALUES (?, ?, ?)",
                  (title, url, order_index))
        conn.commit()
        link_id = c.lastrowid
    
    return {"success": True, "id": link_id}

@app.route("/api/links/<int:link_id>", methods=["PUT"])
def update_link(link_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    data = request.get_json()
    title = data.get("title")
    url = data.get("url")
    order_index = data.get("order_index", 0)
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("UPDATE links SET title=?, url=?, order_index=? WHERE id=?",
                  (title, url, order_index, link_id))
        conn.commit()
    
    return {"success": True}

@app.route("/api/links/<int:link_id>", methods=["DELETE"])
def delete_link(link_id):
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("DELETE FROM links WHERE id=?", (link_id,))
        conn.commit()
    
    return {"success": True}

# --- SITE INFO MANAGEMENT API ---
@app.route("/api/site-info", methods=["GET"])
def get_site_info():
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("SELECT key, value FROM site_info")
        info = {row[0]: row[1] for row in c.fetchall()}
    return {"site_info": info}

@app.route("/api/site-info", methods=["PUT"])
def update_site_info():
    if not is_admin():
        return {"error": "Yetkiniz yok"}, 403
    
    data = request.get_json()
    
    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        for key, value in data.items():
            c.execute("INSERT OR REPLACE INTO site_info (key, value) VALUES (?, ?)", (key, value))
        conn.commit()
    
    return {"success": True}

# --- Kullanıcıları listele (sadece izinli kullanıcı) ---
@app.route("/admin/users")
def list_users():
    if "username" not in session or session["username"] not in ALLOWED_ADMIN_USERNAMES:
        flash("Yetkiniz yok.")
        return redirect(url_for("login"))

    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("""SELECT id, role, username, name, surname, birthdate, gender, class, subject,
                            phone, school_number, school_name, gmail, profile_picture
                     FROM users""")
        users = c.fetchall()

    return render_template("users.html", users=users)

@app.route("/admin/export_users")
def export_users():
    if "username" not in session or session["username"] not in ALLOWED_ADMIN_USERNAMES:
        flash("Yetkiniz yok.")
        return redirect(url_for("login"))

    with sqlite3.connect(DB_NAME) as conn:
        c = conn.cursor()
        c.execute("""SELECT id, role, username, name, surname, birthdate, gender, class, subject,
                            phone, school_number, school_name, gmail, profile_picture
                     FROM users""")
        users = c.fetchall()

    # CSV oluştur
    def generate():
        data = csv.writer([])
        header = ["ID", "Role", "Username", "Name", "Surname", "Birthdate", "Gender", "Class",
                  "Subject", "Phone", "School Number", "School Name", "Gmail", "Profile Picture"]
        yield ",".join(header) + "\n"
        for u in users:
            row = [str(i) if i is not None else "" for i in u]
            yield ",".join(row) + "\n"

    return Response(generate(), mimetype="text/csv",
                    headers={"Content-Disposition": "attachment;filename=users.csv"})

# --- Logout ---
@app.route("/logout")
def logout():
    session.clear()
    flash("Çıkış yapıldı.")
    return redirect(url_for("login"))

if __name__ == "__main__":
    init_db()
    # migrate_users_table()  # sadece yeni column eklemek icin
    app.run(host="0.0.0.0", port=5000)
