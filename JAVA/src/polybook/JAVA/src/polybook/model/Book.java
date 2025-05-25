package polybook.model;

import java.util.Date;

/**
 * Classe modèle pour représenter un livre
 */
public class Book {
    private int id;
    private Date createdAt;
    private String title;
    private String author;
    private String isbn;
    private int publicationYear;
    private String description;
    private String language;
    private int addedByUserId;
    private boolean isApproved;
    
    // Constructeur par défaut
    public Book() {
    }
    
    // Constructeur avec paramètres
    public Book(String title, String author, String isbn, int publicationYear, 
               String description, String language, int addedByUserId) {
        this.title = title;
        this.author = author;
        this.isbn = isbn;
        this.publicationYear = publicationYear;
        this.description = description;
        this.language = language;
        this.addedByUserId = addedByUserId;
        this.isApproved = false;
        this.createdAt = new Date();
    }
    
    // Getters et Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    
    public Date getCreatedAt() { return createdAt; }
    public void setCreatedAt(Date createdAt) { this.createdAt = createdAt; }
    
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    
    public String getAuthor() { return author; }
    public void setAuthor(String author) { this.author = author; }
    
    public String getIsbn() { return isbn; }
    public void setIsbn(String isbn) { this.isbn = isbn; }
    
    public int getPublicationYear() { return publicationYear; }
    public void setPublicationYear(int publicationYear) { this.publicationYear = publicationYear; }
    
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    
    public String getLanguage() { return language; }
    public void setLanguage(String language) { this.language = language; }
    
    public int getAddedByUserId() { return addedByUserId; }
    public void setAddedByUserId(int addedByUserId) { this.addedByUserId = addedByUserId; }
    
    public boolean isApproved() { return isApproved; }
    public void setApproved(boolean approved) { isApproved = approved; }
    
    @Override
    public String toString() {
        return title + " par " + author + " (" + publicationYear + ")";
    }
}
