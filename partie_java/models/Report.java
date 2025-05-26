package partie_java.models;

public class Report {
    private int id;
    private int reportedByUserId;
    private int reportedUserId;
    private String reason;
    private String status;

    public Report(int id, int reportedByUserId, int reportedUserId, String reason, String status) {
        this.id = id;
        this.reportedByUserId = reportedByUserId;
        this.reportedUserId = reportedUserId;
        this.reason = reason;
        this.status = status;
    }

    public int getId() { return id; }
    public int getReportedByUserId() { return reportedByUserId; }
    public int getReportedUserId() { return reportedUserId; }
    public String getReason() { return reason; }
    public String getStatus() { return status; }
}
