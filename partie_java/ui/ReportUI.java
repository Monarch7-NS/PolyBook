package partie_java.ui;

import partie_java.dao.ReportDAO;
import partie_java.models.Report;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class ReportUI extends JFrame {
    public ReportUI() {
        setTitle("Signalements reçus");
        setSize(500, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea area = new JTextArea();
        area.setEditable(false);
        add(new JScrollPane(area), BorderLayout.CENTER);

        ReportDAO dao = new ReportDAO();
        List<Report> reports = dao.getAllReports();

        for (Report r : reports) {
            area.append("🛑 Raison : " + r.getReason() + " | Statut : " + r.getStatus() + "\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new ReportUI().setVisible(true));
    }
}
