package partie_java.models;

public class FavoriteBook {
    private int id;
    private int userId;
    private int bookId;

    public FavoriteBook(int id, int userId, int bookId) {
        this.id = id;
        this.userId = userId;
        this.bookId = bookId;
    }

    public int getId() { return id; }
    public int getUserId() { return userId; }
    public int getBookId() { return bookId; }
}
