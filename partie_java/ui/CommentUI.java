package partie_java.ui;

import partie_java.dao.CommentDAO;
import partie_java.models.Comment;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class CommentUI extends JFrame {
    public CommentUI(int reviewId) {
        setTitle("Commentaires sur l'avis");
        setSize(500, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea commentArea = new JTextArea();
        commentArea.setEditable(false);
        add(new JScrollPane(commentArea), BorderLayout.CENTER);

        CommentDAO dao = new CommentDAO();
        List<Comment> comments = dao.getCommentsForReview(reviewId);

        for (Comment c : comments) {
            commentArea.append(c.getContent() + "\n--------------------\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new CommentUI(1).setVisible(true));
    }
}
