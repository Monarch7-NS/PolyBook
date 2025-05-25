package polybook.ui;

import polybook.DatabaseConnection;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.*;
import java.util.Vector;

/**
 * Interface principale d'administration PolyBook
 * Permet la gestion des livres, utilisateurs et statistiques
 */
public class AdminFrame extends JFrame {
    
    private JTabbedPane tabbedPane;
    private JTable booksTable;
    private JTable usersTable;
    private DefaultTableModel booksModel;
    private DefaultTableModel usersModel;
    
    public AdminFrame() {
        setTitle("PolyBook - Interface d'Administration");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(1000, 700);
        setLocationRelativeTo(null);
        
        initializeComponents();
        setupLayout();
        loadData();
    }
    
    private void initializeComponents() {
        tabbedPane = new JTabbedPane();
        
        // Onglet Livres
        JPanel booksPanel = createBooksPanel();
        tabbedPane.addTab("📚 Gestion des Livres", booksPanel);
        
        // Onglet Utilisateurs
        JPanel usersPanel = createUsersPanel();
        tabbedPane.addTab("👥 Gestion des Utilisateurs", usersPanel);
        
        // Onglet Statistiques
        JPanel statsPanel = createStatsPanel();
        tabbedPane.addTab("📊 Statistiques", statsPanel);
    }
    
    private JPanel createBooksPanel() {
        JPanel panel = new JPanel(new BorderLayout());
        
        // Table des livres
        String[] bookColumns = {"ID", "Titre", "Auteur", "ISBN", "Année", "Langue", "Approuvé"};
        booksModel = new DefaultTableModel(bookColumns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 6; // Seule la colonne "Approuvé" est éditable
            }
            
            @Override
            public Class<?> getColumnClass(int column) {
                return column == 6 ? Boolean.class : String.class;
            }
        };
        
        booksTable = new JTable(booksModel);
        booksTable.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        
        JScrollPane booksScrollPane = new JScrollPane(booksTable);
        panel.add(booksScrollPane, BorderLayout.CENTER);
        
        // Panel des boutons
        JPanel buttonPanel = new JPanel(new FlowLayout());
        
        JButton addBookBtn = new JButton("➕ Ajouter Livre");
        addBookBtn.addActionListener(e -> showAddBookDialog());
        
        JButton deleteBookBtn = new JButton("🗑️ Supprimer Livre");
        deleteBookBtn.addActionListener(e -> deleteSelectedBook());
        
        JButton refreshBtn = new JButton("🔄 Actualiser");
        refreshBtn.addActionListener(e -> loadBooksData());
        
        JButton approveBtn = new JButton("✅ Approuver/Rejeter");
        approveBtn.addActionListener(e -> toggleBookApproval());
        
        buttonPanel.add(addBookBtn);
        buttonPanel.add(deleteBookBtn);
        buttonPanel.add(approveBtn);
        buttonPanel.add(refreshBtn);
        
        panel.add(buttonPanel, BorderLayout.SOUTH);
        
        return panel;
    }
    
    private JPanel createUsersPanel() {
        JPanel panel = new JPanel(new BorderLayout());
        
        // Table des utilisateurs
        String[] userColumns = {"ID", "Nom d'utilisateur", "Email", "Rôle", "Grade", "Date création"};
        usersModel = new DefaultTableModel(userColumns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };
        
        usersTable = new JTable(usersModel);
        usersTable.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        
        JScrollPane usersScrollPane = new JScrollPane(usersTable);
        panel.add(usersScrollPane, BorderLayout.CENTER);
        
        // Panel des boutons
        JPanel buttonPanel = new JPanel(new FlowLayout());
        
        JButton addUserBtn = new JButton("➕ Ajouter Utilisateur");
        addUserBtn.addActionListener(e -> showAddUserDialog());
        
        JButton deleteUserBtn = new JButton("🗑️ Supprimer Utilisateur");
        deleteUserBtn.addActionListener(e -> deleteSelectedUser());
        
        JButton refreshUsersBtn = new JButton("🔄 Actualiser");
        refreshUsersBtn.addActionListener(e -> loadUsersData());
        
        buttonPanel.add(addUserBtn);
        buttonPanel.add(deleteUserBtn);
        buttonPanel.add(refreshUsersBtn);
        
        panel.add(buttonPanel, BorderLayout.SOUTH);
        
        return panel;
    }
    
    private JPanel createStatsPanel() {
        JPanel panel = new JPanel(new GridLayout(4, 2, 10, 10));
        panel.setBorder(BorderFactory.createTitledBorder("Statistiques de la plateforme"));
        
        // Statistiques générales
        try {
            Connection conn = DatabaseConnection.getConnection();
            
            // Nombre total de livres
            PreparedStatement stmt = conn.prepareStatement("SELECT COUNT(*) FROM book WHERE is_approved = TRUE");
            ResultSet rs = stmt.executeQuery();
            int totalBooks = rs.next() ? rs.getInt(1) : 0;
            panel.add(new JLabel("Livres approuvés:"));
            panel.add(new JLabel(String.valueOf(totalBooks)));
            
            // Nombre total d'utilisateurs
            stmt = conn.prepareStatement("SELECT COUNT(*) FROM user");
            rs = stmt.executeQuery();
            int totalUsers = rs.next() ? rs.getInt(1) : 0;
            panel.add(new JLabel("Utilisateurs:"));
            panel.add(new JLabel(String.valueOf(totalUsers)));
            
            // Nombre total d'avis
            stmt = conn.prepareStatement("SELECT COUNT(*) FROM review");
            rs = stmt.executeQuery();
            int totalReviews = rs.next() ? rs.getInt(1) : 0;
            panel.add(new JLabel("Avis publiés:"));
            panel.add(new JLabel(String.valueOf(totalReviews)));
            
            // Livres en attente d'approbation
            stmt = conn.prepareStatement("SELECT COUNT(*) FROM book WHERE is_approved = FALSE");
            rs = stmt.executeQuery();
            int pendingBooks = rs.next() ? rs.getInt(1) : 0;
            panel.add(new JLabel("Livres en attente:"));
            panel.add(new JLabel(String.valueOf(pendingBooks)));
            
        } catch (SQLException e) {
            panel.add(new JLabel("Erreur de chargement des statistiques"));
            panel.add(new JLabel(e.getMessage()));
        }
        
        return panel;
    }
    
    private void setupLayout() {
        setLayout(new BorderLayout());
        add(tabbedPane, BorderLayout.CENTER);
        
        // Barre de statut
        JLabel statusBar = new JLabel("Prêt - Connexion à la base de données: " + 
            (DatabaseConnection.testConnection() ? "✅ OK" : "❌ ERREUR"));
        statusBar.setBorder(BorderFactory.createLoweredBevelBorder());
        add(statusBar, BorderLayout.SOUTH);
    }
    
    private void loadData() {
        loadBooksData();
        loadUsersData();
    }
    
    private void loadBooksData() {
        booksModel.setRowCount(0);
        try {
            Connection conn = DatabaseConnection.getConnection();
            String query = "SELECT id, title, author, isbn, publication_year, language, is_approved FROM book ORDER BY created_at DESC";
            PreparedStatement stmt = conn.prepareStatement(query);
            ResultSet rs = stmt.executeQuery();
            
            while (rs.next()) {
                Vector<Object> row = new Vector<>();
                row.add(rs.getInt("id"));
                row.add(rs.getString("title"));
                row.add(rs.getString("author"));
                row.add(rs.getString("isbn"));
                row.add(rs.getInt("publication_year"));
                row.add(rs.getString("language"));
                row.add(rs.getBoolean("is_approved"));
                booksModel.addRow(row);
            }
            
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Erreur lors du chargement des livres: " + e.getMessage());
        }
    }
    
    private void loadUsersData() {
        usersModel.setRowCount(0);
        try {
            Connection conn = DatabaseConnection.getConnection();
            String query = "SELECT u.id, u.username, u.email, u.role, g.name as grade_name, u.created_at " +
                          "FROM user u LEFT JOIN grade g ON u.grade_id = g.id ORDER BY u.created_at DESC";
            PreparedStatement stmt = conn.prepareStatement(query);
            ResultSet rs = stmt.executeQuery();
            
            while (rs.next()) {
                Vector<Object> row = new Vector<>();
                row.add(rs.getInt("id"));
                row.add(rs.getString("username"));
                row.add(rs.getString("email"));
                row.add(rs.getString("role"));
                row.add(rs.getString("grade_name"));
                row.add(rs.getDate("created_at"));
                usersModel.addRow(row);
            }
            
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Erreur lors du chargement des utilisateurs: " + e.getMessage());
        }
    }
    
    private void showAddBookDialog() {
        JOptionPane.showMessageDialog(this, "Fonctionnalité d'ajout de livre à implémenter");
    }
    
    private void deleteSelectedBook() {
        int selectedRow = booksTable.getSelectedRow();
        if (selectedRow == -1) {
            JOptionPane.showMessageDialog(this, "Veuillez sélectionner un livre à supprimer.");
            return;
        }
        JOptionPane.showMessageDialog(this, "Fonctionnalité de suppression à implémenter");
    }
    
    private void toggleBookApproval() {
        int selectedRow = booksTable.getSelectedRow();
        if (selectedRow == -1) {
            JOptionPane.showMessageDialog(this, "Veuillez sélectionner un livre.");
            return;
        }
        JOptionPane.showMessageDialog(this, "Fonctionnalité d'approbation à implémenter");
    }
    
    private void showAddUserDialog() {
        JOptionPane.showMessageDialog(this, "Fonctionnalité d'ajout d'utilisateur à implémenter");
    }
    
    private void deleteSelectedUser() {
        JOptionPane.showMessageDialog(this, "Fonctionnalité de suppression d'utilisateur à implémenter");
    }
}
