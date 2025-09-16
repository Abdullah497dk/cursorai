from flask import Flask, render_template, request, redirect, session, url_for
import json
import os

app = Flask(__name__)
app.secret_key = "gizli_sifre"  # Şifreli giriş için gerekli
DATA_DIR = os.path.join(os.path.dirname(__file__), 'data')

def load_data(filename):
    try:
        with open(os.path.join(DATA_DIR, filename), 'r', encoding='utf-8') as f:
            return json.load(f)
    except:
        return []

def save_data(filename, data):
    with open(os.path.join(DATA_DIR, filename), 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=4, ensure_ascii=False)

@app.route('/')
def index():
    if not session.get("logged_in"):
        return redirect('/login')
    videolar = load_data('videolar.json')
    dokumanlar = load_data('dokumanlar.json')
    linkler = load_data('linkler.json')
    return render_template('admin.html', videolar=videolar, dokumanlar=dokumanlar, linkler=linkler)

@app.route('/login', methods=["GET", "POST"])
def login():
    if request.method == "POST":
        username = request.form["username"]
        password = request.form["password"]
        if username == "admin" and password == "1234":
            session["logged_in"] = True
            return redirect('/')
        else:
            return "Hatalı giriş."
    return render_template('login.html')

@app.route('/logout')
def logout():
    session["logged_in"] = False
    return redirect('/login')

@app.route('/add', methods=["POST"])
def add():
    if not session.get("logged_in"):
        return redirect('/login')
    kategori = request.form['kategori']
    baslik = request.form['baslik']
    link = request.form['link']
    aciklama = request.form['aciklama']

    filename = kategori + '.json'
    data = load_data(filename)
    data.append({
        "baslik": baslik,
        "link": link,
        "aciklama": aciklama
    })
    save_data(filename, data)
    return redirect('/')
