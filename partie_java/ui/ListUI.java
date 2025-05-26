package partie_java.ui;

import partie_java.dao.ReadingListDAO;
import partie_java.models.ReadingList;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class ListUI extends JFrame {
    public ListUI(int userId) {
        setTitle("Mes listes de lecture");
        setSize(500, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea listArea = new JTextArea();
        listArea.setEditable(false);
        add(new JScrollPane(listArea), BorderLayout.CENTER);

        ReadingListDAO dao = new ReadingListDAO();
        List<ReadingList> lists = dao.getListsForUser(userId);

        for (ReadingList l : lists) {
            listArea.append("📚 " + l.getName() + (l.isPublic() ? " (publique)" : " (privée)") + "\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new ListUI(1).setVisible(true));
    }
}
