

package partie_java.ui;

import partie_java.dao.BookDAO;
import partie_java.models.Book;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class BookCatalogUI extends JFrame {
    public BookCatalogUI() {
        setTitle("Catalogue des livres");
        setSize(600, 400);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea bookArea = new JTextArea();
        bookArea.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(bookArea);
        add(scrollPane, BorderLayout.CENTER);

        BookDAO dao = new BookDAO();
        List<Book> books = dao.getAllBooks();

        for (Book book : books) {
            bookArea.append("ID: " + book.getId() +
                            " | Titre: " + book.getTitle() +
                            " | Auteur: " + book.getAuthor() +
                            " | Langue: " + book.getLanguage() + "\\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new BookCatalogUI().setVisible(true));
    }
}

