from fastapi import FastAPI, Depends, HTTPException
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Column, Integer, Float, String, Date
from typing import List

# Buat objek FastAPI
app = FastAPI()

# Konfigurasi koneksi ke PostgreSQL
SQLALCHEMY_DATABASE_URL = "postgresql://postgres:admin@localhost/postgres"

# Membuat engine database
engine = create_engine(SQLALCHEMY_DATABASE_URL)

# Membuat sesi database
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Membuat base class untuk model
Base = declarative_base()

# Definisi model untuk tabel 'posts'
class Post(Base):
    __tablename__ = 'posts'
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer)
    provinsi = Column(Integer)
    kabupaten = Column(Integer)
    temperature = Column(Float)
    rainfall = Column(Float)
    humidity = Column(Float)
    windspeed = Column(Float)
    date = Column(Date)
    published_at = Column(String)
    created_at = Column(String)
    updated_at = Column(String)

# Mendefinisikan fungsi untuk mendapatkan sesi database
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Route untuk mendapatkan data dari tabel posts
@app.get("/posts/", response_model=List[Post])
def read_posts(skip: int = 0, limit: int = 10, db: Session = Depends(get_db)):
    posts = db.query(Post).offset(skip).limit(limit).all()
    return posts

# Route untuk mendapatkan data spesifik dari tabel posts berdasarkan ID
@app.get("/posts/{post_id}", response_model=Post)
def read_post(post_id: int, db: Session = Depends(get_db)):
    post = db.query(Post).filter(Post.id == post_id).first()
    if post is None:
        raise HTTPException(status_code=404, detail="Post not found")
    return post
