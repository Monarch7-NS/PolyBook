package partie_java.models;

public class ReadingGroup {
    private int id;
    private String name;
    private String description;
    private int creatorUserId;

    public ReadingGroup(int id, String name, String description, int creatorUserId) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.creatorUserId = creatorUserId;
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public String getDescription() { return description; }
    public int getCreatorUserId() { return creatorUserId; }
}
