package partie_java.models;

public class Circle {
    private int id;
    private String name;
    private int ownerUserId;

    public Circle(int id, String name, int ownerUserId) {
        this.id = id;
        this.name = name;
        this.ownerUserId = ownerUserId;
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public int getOwnerUserId() { return ownerUserId; }
}
