<?php

declare(strict_types=1);

class AdminModel extends Model
{


    public function deleteUser(int $id): bool
    {
        $this->db->query("DELETE FROM blog_users WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function editArticle(int $id, array $data): bool
    {
        $this->db->query("UPDATE blog_articles SET title = :title, body = :body WHERE id = :id");
        $this->db->bind(":title", $data['title']);
        $this->db->bind(":body", $data['body']);
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function assignRole(int $userId, int $roleId): bool
    {
        $result = $this->db->dbInsert('blog_user_roles', [
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
        return $result;
    }

    // get all users
    public function getUsers(): array
    {
        $this->db->query("SELECT * FROM blog_users");
        $this->db->execute();
        return $this->db->fetchRows();
    }

    // get all roles
    public function getRoles(): array
    {
        $this->db->query("SELECT * FROM blog_roles");
        $this->db->execute();
        return $this->db->fetchRows();
    }

    // get all categories
    public function getCategories(): array
    {
        $this->db->query("SELECT * FROM blog_categories");
        $this->db->execute();
        return $this->db->fetchRows();
    }

    // get all tags
    public function getTags(): array
    {
        $this->db->query("SELECT * FROM blog_tags");
        $this->db->execute();
        return $this->db->fetchRows();
    }

    // get all articles
    public function getArticles(): array
    {
        $this->db->query("SELECT * FROM blog_articles");
        $this->db->execute();
        return $this->db->fetchRows();
    }


    // get all comments
    public function getComments(): array
    {
        $this->db->query("SELECT * FROM blog_comments");
        $this->db->execute();
        return $this->db->fetchRows();
    }

    // get all user admin 
    public function getUserAdmin(): array
    {
        $this->db->query("SELECT blog_users.* FROM blog_user_roles  
        INNER JOIN blog_users ON blog_user_roles.user_id = blog_users.id 
        INNER JOIN blog_roles ON blog_user_roles.role_id = blog_roles.id
        WHERE blog_roles.role_name = 'admin'");
        $this->db->execute();
        return $this->db->fetchRows();
    }


    public function deleteComment(int $id): bool
    {
        $this->db->query("DELETE FROM blog_comments WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function deleteArticle(int $id): bool
    {
        $this->db->query("DELETE FROM blog_articles WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function deleteRole(int $id): bool
    {
        $this->db->query("DELETE FROM blog_roles WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function deleteTag(int $id): bool
    {
        $this->db->query("DELETE FROM blog_tags WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }

    public function deleteArticleTag(int $id): bool
    {
        $this->db->query("DELETE FROM blog_article_tags WHERE id = :id");
        $this->db->bind(":id", $id);
        return $this->db->execute();
    }
}
