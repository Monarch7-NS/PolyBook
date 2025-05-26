package partie_java.ui;

import partie_java.dao.ReadingGroupDAO;
import partie_java.models.ReadingGroup;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class GroupUI extends JFrame {
    public GroupUI() {
        setTitle("Groupes de lecture");
        setSize(500, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea groupArea = new JTextArea();
        groupArea.setEditable(false);
        add(new JScrollPane(groupArea), BorderLayout.CENTER);

        ReadingGroupDAO dao = new ReadingGroupDAO();
        List<ReadingGroup> groups = dao.getAllGroups();

        for (ReadingGroup g : groups) {
            groupArea.append("📘 " + g.getName() + "\n" + g.getDescription() + "\n\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new GroupUI().setVisible(true));
    }
}
