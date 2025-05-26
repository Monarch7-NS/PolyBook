package partie_java.models;

public class Review {
    private int id;
    private int userId;
    private int bookId;
    private double rating;
    private String content;

    public Review(int id, int userId, int bookId, double rating, String content) {
        this.id = id;
        this.userId = userId;
        this.bookId = bookId;
        this.rating = rating;
        this.content = content;
    }

    public int getId() { return id; }
    public int getUserId() { return userId; }
    public int getBookId() { return bookId; }
    public double getRating() { return rating; }
    public String getContent() { return content; }
}
