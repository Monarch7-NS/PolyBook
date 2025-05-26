package partie_java.models;

public class ReadingList {
    private int id;
    private String name;
    private int userId;
    private boolean isPublic;

    public ReadingList(int id, String name, int userId, boolean isPublic) {
        this.id = id;
        this.name = name;
        this.userId = userId;
        this.isPublic = isPublic;
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public int getUserId() { return userId; }
    public boolean isPublic() { return isPublic; }
}
