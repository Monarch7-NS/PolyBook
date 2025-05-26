package partie_java.models;

public class Book {
    private int id;
    private String title;
    private String author;
    private String language;
    private boolean isApproved;

    public Book(int id, String title, String author, String language, boolean isApproved) {
        this.id = id;
        this.title = title;
        this.author = author;
        this.language = language;
        this.isApproved = isApproved;
    }

    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getAuthor() { return author; }
    public String getLanguage() { return language; }
    public boolean isApproved() { return isApproved; }

    public void setTitle(String title) { this.title = title; }
    public void setAuthor(String author) { this.author = author; }
    public void setLanguage(String language) { this.language = language; }
    public void setApproved(boolean approved) { isApproved = approved; }
}
