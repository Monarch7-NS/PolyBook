package partie_java.ui;

import partie_java.dao.ReviewDAO;
import partie_java.models.Review;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class ReviewUI extends JFrame {
    public ReviewUI(int bookId) {
        setTitle("Avis sur le livre");
        setSize(500, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea reviewArea = new JTextArea();
        reviewArea.setEditable(false);
        add(new JScrollPane(reviewArea), BorderLayout.CENTER);

        ReviewDAO dao = new ReviewDAO();
        List<Review> reviews = dao.getReviewsForBook(bookId);

        for (Review r : reviews) {
            reviewArea.append("Note: " + r.getRating() + "/5\n" +
                              r.getContent() + "\n--------------------\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new ReviewUI(1).setVisible(true));
    }
}
