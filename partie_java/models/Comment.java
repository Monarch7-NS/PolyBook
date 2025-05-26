package partie_java.models;

public class Comment {
    private int id;
    private int userId;
    private int reviewId;
    private String content;

    public Comment(int id, int userId, int reviewId, String content) {
        this.id = id;
        this.userId = userId;
        this.reviewId = reviewId;
        this.content = content;
    }

    public int getId() { return id; }
    public int getUserId() { return userId; }
    public int getReviewId() { return reviewId; }
    public String getContent() { return content; }
}
